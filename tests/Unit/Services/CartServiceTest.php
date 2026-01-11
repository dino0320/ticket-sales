<?php

namespace Tests\Unit\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CartServiceTest extends TestCase
{
    /**
     * Test getCartId() when user exists
     */
    public function test_get_cart_id_user(): void
    {
        $user = User::factory()->make(['id' => 1]);
        $this->assertSame("1", CartService::getCartId($user));
    }

    /**
     * Test getCartId() when user does not exist
     */
    public function test_get_cart_id_guest(): void
    {
        $guestCartId = CartService::getCartId(null);
        $this->assertSame($guestCartId, CartService::getCartId(null));
    }

    /**
     * Test getTotalPrice()
     */
    public function test_get_total_price(): void
    {
        $tickets = new Collection();
        $tickets->add(Ticket::factory()->make(['id' => 1, 'price' => 100]));
        $tickets->add(Ticket::factory()->make(['id' => 2, 'price' => 200]));
        $tickets->add(Ticket::factory()->make(['id' => 3, 'price' => 300]));
        $numbersOfTickets = [
            1 => 10,
            2 => 20,
            3 => 30,
        ];
        $this->assertSame(14000, CartService::getTotalPrice($tickets, $numbersOfTickets));
    }

    /**
     * Test getDifferenceInTotalPrice()
     */
    public function test_get_difference_in_total_price(): void
    {
        $ticket = Ticket::factory()->make(['price' => 100]);
        $this->assertSame(300, CartService::getDifferenceInTotalPrice(5, 2, $ticket));
        $this->assertSame(-200, CartService::getDifferenceInTotalPrice(1, 3, $ticket));
    }
}
