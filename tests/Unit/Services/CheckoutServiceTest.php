<?php

namespace Tests\Unit\Services;

use App\Jobs\CancelOrder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserOrder;
use App\Services\CheckoutService;
use App\Services\Stripe\StripeService;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\Checkout;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class CheckoutServiceTest extends TestCase
{
    /**
     * Test getStripePriceIds()
     */
    public function test_get_stripe_price_ids(): void
    {
        $userOrder = new UserOrder([
            'order_items' => [
                [
                    'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_1',
                    'number_of_tickets' => 1,
                ],
                [
                    'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_2',
                    'number_of_tickets' => 2,
                ],
                [
                    'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_3',
                    'number_of_tickets' => 3,
                ],
            ],
        ]);
        $expectedStripePriceIds = [
            'TEST_STRIPE_PRICE_ID_1' => 1,
            'TEST_STRIPE_PRICE_ID_2' => 2,
            'TEST_STRIPE_PRICE_ID_3' => 3,
        ];
        $this->assertSame($expectedStripePriceIds, CheckoutService::getStripePriceIds($userOrder));
    }

    /**
     * Test getNumbersOfTickets()
     */
    public function test_get_numbers_of_tickets(): void
    {
        $userOrder = new UserOrder([
            'order_items' => [
                [
                    'ticket_id' => 1,
                    'number_of_tickets' => 1,
                ],
                [
                    'ticket_id' => 2,
                    'number_of_tickets' => 2,
                ],
                [
                    'ticket_id' => 3,
                    'number_of_tickets' => 3,
                ],
            ],
        ]);
        $expectedNumbersOfTickets = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        $this->assertSame($expectedNumbersOfTickets, CheckoutService::getNumbersOfTickets($userOrder));
    }

    /**
     * Test createOrderItems()
     */
    public function test_create_order_items(): void
    {
        $tickets = [
            Ticket::factory()->make([
                'id' => 1,
                'event_title' => 'Test Event 1',
                'event_description' => 'Test Event 1 Description',
                'price' => 100,
                'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_1',
            ]),
            Ticket::factory()->make([
                'id' => 2,
                'event_title' => 'Test Event 2',
                'event_description' => 'Test Event 2 Description',
                'price' => 200,
                'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_2',
            ]),
            Ticket::factory()->make([
                'id' => 3,
                'event_title' => 'Test Event 3',
                'event_description' => 'Test Event 3 Description',
                'price' => 300,
                'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_3',
            ]),
        ];
        $numbersOfTickets = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        $expectedOrderItems = [
            [
                'ticket_id' => 1,
                'event_title' => 'Test Event 1',
                'event_description' => 'Test Event 1 Description',
                'price' => 100,
                'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_1',
                'number_of_tickets' => 1,
            ],
            [
                'ticket_id' => 2,
                'event_title' => 'Test Event 2',
                'event_description' => 'Test Event 2 Description',
                'price' => 200,
                'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_2',
                'number_of_tickets' => 2,
            ],
            [
                'ticket_id' => 3,
                'event_title' => 'Test Event 3',
                'event_description' => 'Test Event 3 Description',
                'price' => 300,
                'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_3',
                'number_of_tickets' => 3,
            ],
        ];
        $this->assertSame($expectedOrderItems, CheckoutService::createOrderItems($tickets, $numbersOfTickets));
    }

    /**
     * Test createUserTickets()
     */
    public function test_create_user_tickets(): void
    {
        $userOrder = new UserOrder([
            'user_id' => 1,
            'order_items' => [
                [
                    'ticket_id' => 1,
                    'number_of_tickets' => 1,
                ],
                [
                    'ticket_id' => 2,
                    'number_of_tickets' => 2,
                ],
                [
                    'ticket_id' => 3,
                    'number_of_tickets' => 3,
                ],
            ],
        ]);
        $userTickets = CheckoutService::createUserTickets($userOrder);
        $this->assertCount(6, $userTickets);
        $this->assertSame(1, $userTickets[0]->user_id);
        $this->assertSame(1, $userTickets[0]->ticket_id);
        $this->assertNull($userTickets[0]->used_at);
        $this->assertSame(1, $userTickets[5]->user_id);
        $this->assertSame(3, $userTickets[5]->ticket_id);
        $this->assertNull($userTickets[5]->used_at);
    }

    /**
     * Test increaseNumbersOfReservedTickets()
     */
    public function test_increase_numbers_of_reserved_tickets(): void
    {
        $tickets = [
            Ticket::factory()->make([
                'id' => 1,
                'number_of_reserved_tickets' => 0,
            ]),
            Ticket::factory()->make([
                'id' => 2,
                'number_of_reserved_tickets' => 1,
            ]),
            Ticket::factory()->make([
                'id' => 3,
                'number_of_reserved_tickets' => 3,
            ]),
        ];
        $numbersOfTickets = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        CheckoutService::increaseNumbersOfReservedTickets($tickets, $numbersOfTickets);
        $this->assertSame(1, $tickets[0]->number_of_reserved_tickets);
        $this->assertSame(3, $tickets[1]->number_of_reserved_tickets);
        $this->assertSame(6, $tickets[2]->number_of_reserved_tickets);
    }

    /**
     * Test normal decreaseNumbersOfTickets()
     */
    public function test_decrease_numbers_of_tickets_normal(): void
    {        
        $tickets = [
            Ticket::factory()->make([
                'id' => 1,
                'number_of_tickets' => 1,
            ]),
            Ticket::factory()->make([
                'id' => 2,
                'number_of_tickets' => 3,
            ]),
            Ticket::factory()->make([
                'id' => 3,
                'number_of_tickets' => 6,
            ]),
        ];
        $numbersOfTickets = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        CheckoutService::decreaseNumbersOfTickets($tickets, $numbersOfTickets);
        $this->assertSame(0, $tickets[0]->number_of_tickets);
        $this->assertSame(1, $tickets[1]->number_of_tickets);
        $this->assertSame(3, $tickets[2]->number_of_tickets);
    }

    /**
     * Test abnormal decreaseNumbersOfTickets()
     */
    public function test_decrease_numbers_of_tickets_abnormal(): void
    {
        $this->expectException(RuntimeException::class);
        $tickets = [Ticket::factory()->make([
            'id' => 1,
            'number_of_tickets' => 1,
        ])];
        $numbersOfTickets = [
            1 => 2,
        ];
        CheckoutService::decreaseNumbersOfTickets($tickets, $numbersOfTickets);
    }

    /**
     * Test normal decreaseNumbersOfReservedTickets()
     */
    public function test_decrease_numbers_of_reserved_tickets_normal(): void
    {        
        $tickets = [
            Ticket::factory()->make([
                'id' => 1,
                'number_of_reserved_tickets' => 1,
            ]),
            Ticket::factory()->make([
                'id' => 2,
                'number_of_reserved_tickets' => 3,
            ]),
            Ticket::factory()->make([
                'id' => 3,
                'number_of_reserved_tickets' => 6,
            ]),
        ];
        $numbersOfTickets = [
            1 => 1,
            2 => 2,
            3 => 3,
        ];
        CheckoutService::decreaseNumbersOfReservedTickets($tickets, $numbersOfTickets);
        $this->assertSame(0, $tickets[0]->number_of_reserved_tickets);
        $this->assertSame(1, $tickets[1]->number_of_reserved_tickets);
        $this->assertSame(3, $tickets[2]->number_of_reserved_tickets);
    }

    /**
     * Test abnormal decreaseNumbersOfReservedTickets()
     */
    public function test_decrease_numbers_of_reserved_tickets_abnormal(): void
    {
        $this->expectException(RuntimeException::class);
        $tickets = [Ticket::factory()->make([
            'id' => 1,
            'number_of_reserved_tickets' => 1,
        ])];
        $numbersOfTickets = [
            1 => 2,
        ];
        CheckoutService::decreaseNumbersOfReservedTickets($tickets, $numbersOfTickets);
    }

    /**
     * Test normal checkout()
     */
    public function test_checkout_normal(): void
    {        
        Queue::fake();

        $checkout = Mockery::mock(Checkout::class);
        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('checkout')
            ->once()
            ->andReturn($checkout);
        $user = User::factory()->make();
        $userOrder = new UserOrder([
            'order_items' => [
                [
                    'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_1',
                    'number_of_tickets' => 1,
                ],
            ],
        ]);
        $userOrder->id = 1;
        $this->assertSame($checkout, CheckoutService::checkout($stripeService, $user, $userOrder));
        
        Queue::assertNotPushed(CancelOrder::class);
    }

    /**
     * Test abnormal checkout()
     */
    public function test_checkout_abnormal(): void
    {        
        Queue::fake();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Checkout failed');

        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('checkout')
            ->once()
            ->andThrow(new \Exception('Checkout failed'));
        $user = User::factory()->make();
        $userOrder = new UserOrder([
            'order_items' => [
                [
                    'stripe_price_id' => 'TEST_STRIPE_PRICE_ID_1',
                    'number_of_tickets' => 1,
                ],
            ],
        ]);
        $userOrder->id = 1;
        CheckoutService::checkout($stripeService, $user, $userOrder);
        
        Queue::assertPushed(CancelOrder::class, function (CancelOrder $job) {
            return $job->getUserOrderId() === 1;
        });
    }
}
