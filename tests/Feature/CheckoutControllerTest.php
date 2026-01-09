<?php

namespace Tests\Feature;

use App\Consts\CartConst;
use App\Consts\CheckoutConst;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Inertia\Testing\AssertableInertia as Assert;
use InvalidArgumentException;
use Laravel\Cashier\Checkout;
use Mockery;
use RuntimeException;
use Stripe\Checkout\Session;
use Tests\TestCase;

class CheckoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Redis::flushDB();
    }

    /**
     * Test show()
     */
    public function test_show(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $startDate = (new Carbon())->addYears(-1);
        $endDate = (new Carbon())->addYear();
        Ticket::factory()->create([
            'id' => 1,
            'price' => 100,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        Ticket::factory()->create([
            'id' => 2,
            'price' => 200,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        Ticket::factory()->create([
            'id' => 3,
            'price' => 300,
            'start_date' => (new Carbon())->addYears(-2),
            'end_date' => (new Carbon())->addYears(-1),
        ]);
        Ticket::factory()->create([
            'id' => 4,
            'price' => 400,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($key, [
            1 => 2,
            3 => 1,
            4 => 1,
        ]);

        $response = $this->actingAs($user, 'web')->get('/review');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Review')
            ->has('tickets', fn (Assert $page) => $page
                ->has('data', 2)
                ->has('data', fn (Assert $page) => $page
                    ->has(0, fn (Assert $page) => $page
                        ->where('id', 1)
                        ->etc()
                    )
                    ->has(1, fn (Assert $page) => $page
                        ->where('id', 4)
                        ->etc()
                    )
                )
                ->whereType('links', 'array')
                ->whereType('meta', 'array')
            )
            ->where('numbersOfTickets', [
                1 => 2,
                3 => 1,
                4 => 1,
            ])
            ->where('totalPriceOfTickets', 6) // ((100 * 2) + 400) / 100 (dollars)
        );
    }

    /**
     * Test showCheckoutSuccess()
     */
    public function test_show_checkout_success(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $userOrder = UserOrder::create([
            'user_id' => $user->id,
            'amount' => 100,
            'order_items' => [],
            'status' => CheckoutConst::ORDER_STATUS_PENDING,
        ]);

        $sessionId = 'TEST_SESSION_ID';
        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('retrieveCheckoutSession')
            ->once()
            ->withArgs([$sessionId])
            ->andReturn([
                'payment_status' => Session::PAYMENT_STATUS_PAID,
                'metadata' => [
                    'user_order_id' => $userOrder->id,
                ],
            ]);

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->get("/checkout/success?session_id={$sessionId}");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('CheckoutSuccess')
            ->where('userOrderId', $userOrder->id)
        );
    }

    /**
     * Test normal checkout()
     */
    public function test_checkout_normal(): void
    {
        Queue::fake();

        $user = User::factory()->create(['id' => 1]);

        $startDate = (new Carbon())->addYears(-1);
        $endDate = (new Carbon())->addYear();
        $ticket1 = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        $ticket2 = Ticket::factory()->create([
            'id' => 2,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 3,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($key, [
            1 => 1,
            2 => 2,
        ]);

        $checkout = Mockery::mock(Checkout::class);
        $checkout->shouldReceive('toResponse');
        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('checkout')
            ->once()
            ->andReturn($checkout);

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->get('/checkout');
        
        $response->assertStatus(200);

        $ticket1->refresh();
        $ticket2->refresh();
        $this->assertSame(1, $ticket1->number_of_reserved_tickets);
        $this->assertSame(5, $ticket2->number_of_reserved_tickets);
        $userOrder = UserOrder::where('user_id', $user->id)->first();
        $this->assertCount(2, $userOrder->order_items);
    }

    /**
     * Test abnormal (no items in cart) checkout()
     */
    public function test_checkout_abnormal_no_items_in_cart(): void
    {
        Queue::fake();
        Exceptions::fake();

        $user = User::factory()->create();

        $checkout = Mockery::mock(Checkout::class);
        $checkout->shouldReceive('toResponse');
        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('checkout')
            ->never()
            ->andReturn($checkout);

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->get('/checkout');
        
        $response->assertStatus(500);
        Exceptions::assertReported(InvalidArgumentException::class);
    }

    /**
     * Test abnormal (no valid tickets in cart) checkout()
     */
    public function test_checkout_abnormal_no_valid_tickets_in_cart(): void
    {
        Queue::fake();
        Exceptions::fake();

        $user = User::factory()->create(['id' => 1]);

        $startDate = (new Carbon())->addYears(-2);
        $endDate = (new Carbon())->addYears(-1);
        Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        Ticket::factory()->create([
            'id' => 2,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 3,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($key, [
            1 => 1,
            2 => 2,
        ]);

        $checkout = Mockery::mock(Checkout::class);
        $checkout->shouldReceive('toResponse');
        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('checkout')
            ->never()
            ->andReturn($checkout);

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->get('/checkout');
        
        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);

        $this->assertEmpty(Redis::hGetAll($key));
    }

    /**
     * Test abnormal (out of stock) checkout()
     */
    public function test_checkout_abnormal_out_of_stock(): void
    {
        Queue::fake();
        Exceptions::fake();

        $user = User::factory()->create(['id' => 1]);

        $startDate = (new Carbon())->addYears(-1);
        $endDate = (new Carbon())->addYear();
        Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
        Ticket::factory()->create([
            'id' => 2,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 3,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($key, [
            1 => 1,
            2 => 3,
        ]);

        $checkout = Mockery::mock(Checkout::class);
        $checkout->shouldReceive('toResponse');
        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('checkout')
            ->never()
            ->andReturn($checkout);

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->get('/checkout');
        
        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }
}
