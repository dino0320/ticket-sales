<?php

namespace Tests\Feature\Controllers;

use App\Consts\CartConst;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CartControllerTest extends TestCase
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

        $response = $this->actingAs($user, 'web')->get('/cart');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Cart')
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
     * Test normal (first ticket) store()
     */
    public function test_store_normal_first_ticket(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $response = $this->actingAs($user, 'web')->post("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertRedirectBack();

        $key = sprintf(CartConst::CART_KEY, $user->id);
        $this->assertSame(3, (int)Redis::hGet($key, $ticket->id));
    }

    /**
     * Test normal (ticket exists) store()
     */
    public function test_store_normal_ticket_exists(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hSet($key, $ticket->id, 1);

        $response = $this->actingAs($user, 'web')->post("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertRedirectBack();

        $this->assertSame(4, (int)Redis::hGet($key, $ticket->id));
    }

    /**
     * Test abnormal (out of sales period) store()
     */
    public function test_store_abnormal_out_of_sales_period(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'start_date' => (new Carbon())->addYears(-2),
            'end_date' => (new Carbon())->addYears(-1),
        ]);

        $response = $this->actingAs($user, 'web')->post("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors('sales_period');
    }

    /**
     * Test abnormal (out of stock) store()
     */
    public function test_store_abnormal_out_of_stock(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 2,
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $response = $this->actingAs($user, 'web')->post("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors('number_of_tickets');
    }

    /**
     * Test normal update()
     */
    public function test_update_normal(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'price' => 100,
            'number_of_tickets' => 5,
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hSet($key, $ticket->id, 1);

        $response = $this->actingAs($user, 'web')->putJson("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertStatus(200);
        $response->assertJson([
            'numberOfTickets' => 3,
            'differenceInTotalPrice' => 2,
        ]);
        $this->assertSame(3, (int)Redis::hGet($key, $ticket->id));
    }

    /**
     * Test abnormal (out of sales period) update()
     */
    public function test_update_abnormal_out_of_sales_period(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 5,
            'start_date' => (new Carbon())->addYears(-2),
            'end_date' => (new Carbon())->addYears(-1),
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hSet($key, $ticket->id, 1);

        $response = $this->actingAs($user, 'web')->putJson("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertStatus(400);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('sales_period')
        );
        $this->assertFalse(Redis::hGet($key, $ticket->id));
    }

    /**
     * Test abnormal (out of stock) update()
     */
    public function test_update_abnormal_out_of_stock(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'number_of_tickets' => 2,
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hSet($key, $ticket->id, 1);

        $response = $this->actingAs($user, 'web')->putJson("/cart/{$ticket->id}", ['number_of_tickets' => 3]);

        $response->assertStatus(400);
        $response->assertJson(fn (AssertableJson $json) => $json
            ->has('number_of_tickets')
        );
    }

    /**
     * Test destroy()
     */
    public function test_destroy(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create(['id' => 1]);

        $key = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hSet($key, $ticket->id, 1);

        $response = $this->actingAs($user, 'web')->delete("/cart/{$ticket->id}");

        $response->assertRedirectBack();
        $this->assertFalse(Redis::hGet($key, $ticket->id));
    }
}
