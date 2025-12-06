<?php

namespace App\Services;

use App\Consts\CartConst;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;

class CartService
{
    /**
     * Get the total price of tickets
     *
     * @param Collection<Ticket> $tickets
     * @param int[] $numberOfTickets
     * @return integer
     */
    public static function getTotalPrice(Collection $tickets, array $numberOfTickets): int
    {
        $totalPrice = 0;
        foreach ($tickets as $ticket) {
            $totalPrice += $ticket->price * $numberOfTickets[$ticket->id];
        }

        return $totalPrice;
    }

    /**
     * Check if the given number is not less than 0 or more than the number of tickets
     *
     * @param integer $numberOfTickets
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfNumberOfTicketsIsValid(int $numberOfTickets, Ticket $ticket): void
    {
        if ($numberOfTickets <= 0 || $numberOfTickets > $ticket->number_of_tickets) {
            throw new InvalidArgumentException("Invalid number_of_tickets. number_of_tickets: {$numberOfTickets}");
        }
    }

    /**
     * Increase user cart
     *
     * @param integer $userId
     * @param integer $ticketId
     * @param integer $numberOfTickets
     * @return void
     */
    public static function increaseUserCart(int $userId, int $ticketId, int $numberOfTickets): void
    {
        $key = self::getUserCartKey($userId);
        Redis::hIncrBy($key, $ticketId, $numberOfTickets);
        Redis::expire($key, CartConst::CART_EXPIRATION);
    }

    /**
     * Get a cart key
     *
     * @param integer $userId
     * @return string
     */
    private static function getUserCartKey(int $userId): string
    {
        return "cart:{$userId}";
    }

    /**
     * Get user carts
     *
     * @param integer $userId
     * @return int[]
     */
    public static function getUserCarts(int $userId): array
    {
        return Redis::hGetAll(self::getUserCartKey($userId));
    }

    /**
     * Get user cart
     *
     * @param integer $userId
     * @param integer $ticketId
     * @return int
     */
    public static function getUserCart(int $userId, int $ticketId): int
    {
        return Redis::hGet(self::getUserCartKey($userId), $ticketId) ?? throw new InvalidArgumentException("Can't get this ticket. ticket_id: {$ticketId}");
    }

    /**
     * Update user cart
     *
     * @param integer $userId
     * @param integer $ticketId
     * @param integer $numberOfTickets
     * @return void
     */
    public static function updateUserCart(int $userId, int $ticketId, int $numberOfTickets): void
    {
        Redis::hSet(self::getUserCartKey($userId), $ticketId, $numberOfTickets);
    }

    /**
     * Get the difference in the total price of tickets
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
     * Delete user cart
     *
     * @param integer $userId
     * @param integer $ticketId
     * @return void
     */
    public static function deleteUserCart(int $userId, int $ticketId): void
    {
        Redis::hDel(self::getUserCartKey($userId), $ticketId);
    }

    /**
     * Delete all user carts
     *
     * @param integer $userId
     * @return void
     */
    public static function deleteAllUserCarts(int $userId): void
    {
        Redis::del(self::getUserCartKey($userId));
    }
}
