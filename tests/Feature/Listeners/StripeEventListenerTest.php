<?php

namespace Tests\Feature\Listeners;

use App\Consts\CartConst;
use App\Consts\CheckoutConst;
use App\Jobs\CancelOrder;
use App\Listeners\StripeEventListener;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\UserTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Laravel\Cashier\Events\WebhookReceived;
use Tests\TestCase;

class StripeEventListenerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Redis::flushDB();
    }

    /**
     * Test normal payment_intent.succeeded
     */
    public function test_payment_intent_succeeded_normal(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $ticket1 = Ticket::factory()->create([
            'id' => 1,
            'event_title' => 'Event Title 1',
            'event_description' => 'Event Description 1',
            'price' => 100,
            'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_1',
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 3,
        ]);
        $ticket2 = Ticket::factory()->create([
            'id' => 2,
            'event_title' => 'Event Title 2',
            'event_description' => 'Event Description 2',
            'price' => 200,
            'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_2',
            'number_of_tickets' => 1,
            'number_of_reserved_tickets' => 1,
        ]);
        $userOrder = UserOrder::create([
            'user_id' => $user->id,
            'amount' => 0,
            'order_items' => [
                [
                    'ticket_id' => $ticket1->id,
                    'event_title' => $ticket1->event_title,
                    'event_description' => $ticket1->event_description,
                    'price' => $ticket1->price,
                    'stripe_price_id' => $ticket1->stripe_price_id,
                    'number_of_tickets' => 3,
                ],
                [
                    'ticket_id' => $ticket2->id,
                    'event_title' => $ticket2->event_title,
                    'event_description' => $ticket2->event_description,
                    'price' => $ticket2->price,
                    'stripe_price_id' => $ticket2->stripe_price_id,
                    'number_of_tickets' => 1,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_PENDING,
        ]);

        $userCartKey = sprintf(CartConst::CART_KEY, $user->id);
        Redis::hMSet($userCartKey, [
            $ticket1->id => 3,
            $ticket2->id => 1,
        ]);

        $listener = new StripeEventListener();
        $listener->handle(new WebhookReceived([
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'amount' => 500,
                    'metadata' => [
                        'user_order_id' => $userOrder->id,
                    ],
                ],
            ],
        ]));

        $userOrder->refresh();
        $this->assertSame(500, $userOrder->amount); // (100 * 3) + 200
        $this->assertSame(CheckoutConst::ORDER_STATUS_COMPLETED, $userOrder->status);
        $userTickets = UserTicket::where('user_id', $user->id)->get();
        $this->assertCount(4, $userTickets);
        $this->assertSame(1, $userTickets[0]->ticket_id);
        $this->assertSame(2, $userTickets[3]->ticket_id);
        $ticket1->refresh();
        $ticket2->refresh();
        $this->assertSame(2, $ticket1->number_of_tickets);
        $this->assertSame(0, $ticket1->number_of_reserved_tickets);
        $this->assertSame(0, $ticket2->number_of_tickets);
        $this->assertSame(0, $ticket2->number_of_reserved_tickets);
        $this->assertEmpty(Redis::hGetAll($userCartKey));
    }

    /**
     * Test abnormal (order status is completed) payment_intent.succeeded
     */
    public function test_payment_intent_succeeded_abnormal_order_status_is_completed(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'stripe_price_id' => 'TEST_STRIPE_PRICE_ID',
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 3,
        ]);
        $userOrder = UserOrder::create([
            'user_id' => $user->id,
            'amount' => 0,
            'order_items' => [
                [
                    'ticket_id' => $ticket->id,
                    'event_title' => $ticket->event_title,
                    'event_description' => $ticket->event_description,
                    'price' => $ticket->price,
                    'stripe_price_id' => $ticket->stripe_price_id,
                    'number_of_tickets' => 3,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_COMPLETED,
        ]);

        $listener = new StripeEventListener();
        $listener->handle(new WebhookReceived([
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'amount' => 500,
                    'metadata' => [
                        'user_order_id' => $userOrder->id,
                    ],
                ],
            ],
        ]));

        // Check if nothing has been changed

        $userOrder->refresh();
        $this->assertSame(0, $userOrder->amount);
        $this->assertSame(CheckoutConst::ORDER_STATUS_COMPLETED, $userOrder->status);
        $this->assertEmpty(UserTicket::where('user_id', $user->id)->get());
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(3, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (order status is canceled) payment_intent.succeeded
     */
    public function test_payment_intent_succeeded_abnormal_order_status_is_canceled(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'stripe_price_id' => 'TEST_STRIPE_PRICE_ID',
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 3,
        ]);
        $userOrder = UserOrder::create([
            'user_id' => $user->id,
            'amount' => 0,
            'order_items' => [
                [
                    'ticket_id' => $ticket->id,
                    'event_title' => $ticket->event_title,
                    'event_description' => $ticket->event_description,
                    'price' => $ticket->price,
                    'stripe_price_id' => $ticket->stripe_price_id,
                    'number_of_tickets' => 3,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_CANCELED,
        ]);

        $listener = new StripeEventListener();
        $listener->handle(new WebhookReceived([
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'amount' => 500,
                    'metadata' => [
                        'user_order_id' => $userOrder->id,
                    ],
                ],
            ],
        ]));

        // Check if nothing has been changed

        $userOrder->refresh();
        $this->assertSame(0, $userOrder->amount);
        $this->assertSame(CheckoutConst::ORDER_STATUS_CANCELED, $userOrder->status);
        $this->assertEmpty(UserTicket::where('user_id', $user->id)->get());
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(3, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (out of stock) payment_intent.succeeded
     */
    public function test_payment_intent_succeeded_abnormal_out_of_stock(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'stripe_price_id' => 'TEST_STRIPE_PRICE_ID',
            'number_of_tickets' => 2,
            'number_of_reserved_tickets' => 3,
        ]);
        $userOrder = UserOrder::create([
            'user_id' => $user->id,
            'amount' => 0,
            'order_items' => [
                [
                    'ticket_id' => $ticket->id,
                    'event_title' => $ticket->event_title,
                    'event_description' => $ticket->event_description,
                    'price' => $ticket->price,
                    'stripe_price_id' => $ticket->stripe_price_id,
                    'number_of_tickets' => 3,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_PENDING,
        ]);

        $listener = new StripeEventListener();
        $listener->handle(new WebhookReceived([
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'amount' => 500,
                    'metadata' => [
                        'user_order_id' => $userOrder->id,
                    ],
                ],
            ],
        ]));

        // Check if nothing has been changed

        $userOrder->refresh();
        $this->assertSame(0, $userOrder->amount);
        $this->assertSame(CheckoutConst::ORDER_STATUS_PENDING, $userOrder->status);
        $this->assertEmpty(UserTicket::where('user_id', $user->id)->get());
        $ticket->refresh();
        $this->assertSame(2, $ticket->number_of_tickets);
        $this->assertSame(3, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (out of reserved stock) payment_intent.succeeded
     */
    public function test_payment_intent_succeeded_abnormal_out_of_reserved_stock(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'stripe_price_id' => 'TEST_STRIPE_PRICE_ID',
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 2,
        ]);
        $userOrder = UserOrder::create([
            'user_id' => $user->id,
            'amount' => 0,
            'order_items' => [
                [
                    'ticket_id' => $ticket->id,
                    'event_title' => $ticket->event_title,
                    'event_description' => $ticket->event_description,
                    'price' => $ticket->price,
                    'stripe_price_id' => $ticket->stripe_price_id,
                    'number_of_tickets' => 3,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_PENDING,
        ]);

        $listener = new StripeEventListener();
        $listener->handle(new WebhookReceived([
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'amount' => 500,
                    'metadata' => [
                        'user_order_id' => $userOrder->id,
                    ],
                ],
            ],
        ]));

        // Check if nothing has been changed

        $userOrder->refresh();
        $this->assertSame(0, $userOrder->amount);
        $this->assertSame(CheckoutConst::ORDER_STATUS_PENDING, $userOrder->status);
        $this->assertEmpty(UserTicket::where('user_id', $user->id)->get());
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(2, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test checkout.session.expired
     */
    public function test_checkout_session_expired(): void
    {
        Queue::fake();

        $listener = new StripeEventListener();
        $listener->handle(new WebhookReceived([
            'type' => 'checkout.session.expired',
            'data' => [
                'object' => [
                    'metadata' => [
                        'user_order_id' => 1,
                    ],
                ],
            ],
        ]));

        Queue::assertPushed(CancelOrder::class, function (CancelOrder $job) {
            return $job->getUserOrderId() === 1;
        });
    }
}
