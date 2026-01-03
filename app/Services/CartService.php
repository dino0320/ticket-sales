<?php

namespace App\Services;

use App\Consts\CartConst;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;

class CartService
{
    /**
     * Get cart ID
     *
     * @param User|null $user
     * @return string
     */
    public static function getCartId(?User $user): string
    {
        if ($user !== null) {
            return $user->id;
        }
        
        $guestCartId = Session::get('guest_cart_id');
        if ($guestCartId === null) {
            $guestCartId = Str::uuid();
            Session::put('guest_cart_id', $guestCartId);
        }

        return $guestCartId;
    }

    /**
     * Get total price of tickets
     *
     * @param Collection<Ticket> $tickets
     * @param int[] $numberOfTickets
     * @return integer
     */
    public static function getTotalPrice(Collection $tickets, array $numbersOfTickets): int
    {
        $totalPrice = 0;
        foreach ($tickets as $ticket) {
            $totalPrice += $ticket->price * $numbersOfTickets[$ticket->id];
        }

        return $totalPrice;
    }

    /**
     * Get difference in total price of tickets
     *
     * @param integer $preNumberOfTickets
     * @param integer $numberOfTickets
     * @param Ticket $ticket
     * @return integer
     */
    public static function getDifferenceInTotalPrice(int $preNumberOfTickets, int $numberOfTickets, Ticket $ticket): int
    {
        return ($preNumberOfTickets - $numberOfTickets) * $ticket->price;
    }

    /**
     * Get cart key
     *
     * @param string $cartId
     * @return string
     */
    private static function getCartKey(string $cartId): string
    {
        return sprintf(CartConst::CART_KEY, $cartId);
    }

    /**
     * Get cart
     *
     * @param string $cartId
     * @return int[]
     */
    public static function getCart(string $cartId): array
    {
        return Redis::hGetAll(self::getCartKey($cartId));
    }

    /**
     * Get the number of tickets from cart
     *
     * @param string $cartId
     * @param integer $ticketId
     * @return int
     */
    public static function getNumberOfTicketsFromCart(string $cartId, int $ticketId): int
    {
        return Redis::hGet(self::getCartKey($cartId), $ticketId) ?? throw new RuntimeException("Failed to get the number of tickets from cart. cart_id: {$cartId}, ticket_id: {$ticketId}");
    }

    /**
     * Set cart
     *
     * @param string $cartId
     * @param int[] $numbersOfTickets
     * @return void
     */
    private static function setCart(string $cartId, array $numbersOfTickets): void
    {
        $key = self::getCartKey($cartId);
        Redis::hMSet($key, $numbersOfTickets);
        Redis::expire($key, CartConst::CART_EXPIRATION);
    }

    /**
     * Increase the number of tickets in cart
     *
     * @param string $cartId
     * @param integer $ticketId
     * @param integer $numberOfTickets
     * @return void
     */
    public static function increaseNumberOfTicketsInCart(string $cartId, int $ticketId, int $numberOfTickets): void
    {
        $key = self::getCartKey($cartId);
        Redis::hIncrBy($key, $ticketId, $numberOfTickets);
        Redis::expire($key, CartConst::CART_EXPIRATION);
    }

    /**
     * Delete ticket in cart
     *
     * @param string $cartId
     * @param integer $ticketId
     * @return void
     */
    public static function deleteTicketInCart(string $cartId, int $ticketId): void
    {
        Redis::hDel(self::getCartKey($cartId), $ticketId);
    }

    /**
     * Delete cart
     *
     * @param string $cartId
     * @return void
     */
    public static function deleteCart(string $cartId): void
    {
        Redis::del(self::getCartKey($cartId));
    }

    /**
     * Overwrite user cart with guest cart
     *
     * @param User $user
     * @return void
     */
    public static function overwriteUserCartWithGuestCart(User $user): void
    {
        $guestCartId = Session::get('guest_cart_id');
        if ($guestCartId === null) {
            return;
        }

        self::deleteCart($user->id);
        self::setCart($user->id, self::getCart($guestCartId));
    }
}
