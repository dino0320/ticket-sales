<?php

namespace Tests\Feature\Jobs;

use App\Consts\CheckoutConst;
use App\Jobs\CancelOrder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test normal CancelOrder
     */
    public function test_cancel_order_normal(): void
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
            'status' => CheckoutConst::ORDER_STATUS_PENDING,
        ]);

        $job = new CancelOrder($userOrder->id);
        $job->handle();

        $userOrder->refresh();
        $this->assertSame(CheckoutConst::ORDER_STATUS_CANCELED, $userOrder->status);
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(0, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (invalid user order ID) CancelOrder
     */
    public function test_cancel_order_abnormal_invalid_user_order_id(): void
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
            'status' => CheckoutConst::ORDER_STATUS_PENDING,
        ]);

        $job = new CancelOrder($userOrder->id * 100);
        $job->handle();

        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(3, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (order status is completed) CancelOrder
     */
    public function test_cancel_order_abnormal_order_status_is_completed(): void
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

        $job = new CancelOrder($userOrder->id);
        $job->handle();

        $userOrder->refresh();
        $this->assertSame(CheckoutConst::ORDER_STATUS_COMPLETED, $userOrder->status);
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(3, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (order status is canceled) CancelOrder
     */
    public function test_cancel_order_abnormal_order_status_is_canceled(): void
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

        $job = new CancelOrder($userOrder->id);
        $job->handle();

        $userOrder->refresh();
        $this->assertSame(CheckoutConst::ORDER_STATUS_CANCELED, $userOrder->status);
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(3, $ticket->number_of_reserved_tickets);
    }

    /**
     * Test abnormal (out of reserved stock) CancelOrder
     */
    public function test_cancel_order_abnormal_out_of_reserved_stock(): void
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

        $job = new CancelOrder($userOrder->id);
        $job->handle();

        $userOrder->refresh();
        $this->assertSame(CheckoutConst::ORDER_STATUS_PENDING, $userOrder->status);
        $ticket->refresh();
        $this->assertSame(5, $ticket->number_of_tickets);
        $this->assertSame(2, $ticket->number_of_reserved_tickets);
    }
}
