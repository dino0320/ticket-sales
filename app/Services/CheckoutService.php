<?php

namespace App\Services;

use App\Consts\CheckoutConst;
use App\Models\Ticket;
use App\Models\UserOrder;
use App\Models\UserTicket;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;

class CheckoutService
{
    /**
     * Check if the given number is not less than 0 or more than the number of tickets
     *
     * @param integer $numberOfTickets
     * @param Ticket $ticket
     * @param integer $numberOfReservedTickets
     * @return void
     */
    public static function checkIfNumberOfTicketsIsValid(int $numberOfTickets, Ticket $ticket, int $numberOfReservedTickets): void
    {
        if ($numberOfTickets <= 0 || $numberOfTickets > ($ticket->number_of_tickets - $numberOfReservedTickets)) {
            throw new InvalidArgumentException("Invalid number_of_tickets. number_of_tickets: {$numberOfTickets}");
        }
    }

    /**
     * Check if the given numbers are not less than 0 or more than the numbers of tickets
     *
     * @param int[] $numbersOfTickets
     * @param Ticket[] $tickets
     * @param int[] $numbersOfReservedTickets
     * @return void
     */
    public static function checkIfNumbersOfTicketsAreValid(array $numbersOfTickets, array $tickets, array $numbersOfReservedTickets): void
    {
        $tickets = array_column($tickets, null, 'id');
        foreach ($numbersOfTickets as $ticketId => $numberOfTickets) {
            self::checkIfNumberOfTicketsIsValid($numberOfTickets, $tickets[$ticketId], $numbersOfReservedTickets[$ticketId]);
        }
    }

    /**
     * Get order items
     *
     * @param int[] $numberOfTickets
     * @param Ticket[] $tickets
     * @return array
     */
    public static function getOrderItems(array $numbersOfTickets, array $tickets): array
    {
        if ($numbersOfTickets === []) {
            return [];
        }

        $orderItems = [];
        foreach ($tickets as $ticket) {
            $orderItems[] = [
                'ticket_id' => $ticket->id,
                'event_title' => $ticket->event_title,
                'event_description' => $ticket->event_description,
                'price' => $ticket->price,
                'stripe_price_id' => $ticket->stripe_price_id,
                'number_of_tickets' => $numbersOfTickets[$ticket->id],
            ];
        }

        return $orderItems;
    }

    /**
     * Get stripe price ids
     *
     * @param UserOrder $userOrder
     * @return int[]
     */
    public static function getStripePriceIds(UserOrder $userOrder): array
    {
        $stripePriceIds = [];
        foreach ($userOrder->order_items as $orderItem) {
            $stripePriceIds[$orderItem['stripe_price_id']] = ($stripePriceIds[$orderItem['stripe_price_id']] ?? 0) + $orderItem['number_of_tickets'];
        }

        return $stripePriceIds;
    }

    /**
     * Get the numbers of tickets
     *
     * @param UserOrder $userOrder
     * @return int[]
     */
    public static function getNumbersOfTickets(UserOrder $userOrder): array
    {
        return array_column($userOrder->order_items, 'number_of_tickets', 'ticket_id');
    }

    /**
     * Create user tickets
     *
     * @param UserOrder $userOrder
     * @return UserTicket[]
     */
    public static function createUserTickets(UserOrder $userOrder): array
    {
        $userTickets = [];
        foreach ($userOrder->order_items as $orderItem) {
            $userTickets[] = new UserTicket([
                'user_id' => $userOrder->user_id,
                'ticket_id' => $orderItem['ticket_id'],
                'number_of_tickets' => $orderItem['number_of_tickets'],
            ]);
        }

        return $userTickets;
    }

    /**
     * Get a total reserved ticket key
     *
     * @return string
     */
    private static function getTotalReservedTicketKey(): string
    {
        return CheckoutConst::TOTAL_RESERVED_TICKET_KEY;
    }

    /**
     * Get the total number of reserved tickets
     *
     * @param integer $ticketId
     * @return int
     */
    public static function getTotalReservedTicket(int $ticketId): int
    {
        return Redis::hGet(self::getTotalReservedTicketKey(), $ticketId) ?? throw new InvalidArgumentException("Can't get the number of reserved tickets. ticket_id: {$ticketId}");
    }

    /**
     * Get the total numbers of reserved tickets
     *
     * @param int[] $ticketIds
     * @return int[]
     */
    public static function getTotalReservedTickets(array $ticketIds): array
    {
        return array_combine($ticketIds, Redis::hMGet(self::getTotalReservedTicketKey(), $ticketIds));
    }

    /**
     * Increase the total number of reserved tickets
     *
     * @param integer $ticketId
     * @param integer $numberOfTickets
     * @return void
     */
    private static function increaseTotalReservedTicket(int $ticketId, int $numberOfTickets): void
    {
        Redis::hIncrBy(self::getTotalReservedTicketKey(), $ticketId, $numberOfTickets);
    }

    /**
     * Increase the total numbers of reserved tickets
     *
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function increaseTotalReservedTickets(array $numbersOfTickets): void
    {
        foreach ($numbersOfTickets as $tticketId => $numberOfTickets) {
            self::increaseTotalReservedTicket($tticketId, $numberOfTickets);
        }
    }

    /**
     * Decrease the total numbers of reserved tickets
     *
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseTotalReservedTickets(array $numbersOfTickets): void
    {
        foreach ($numbersOfTickets as $tticketId => $numberOfTickets) {
            self::increaseTotalReservedTicket($tticketId, -$numberOfTickets);
        }
    }

    /**
     * Get a reserved ticket key
     *
     * @param integer $ticketId
     * @param integer $userId
     * @return string
     */
    private static function getReservedTicketKey(int $ticketId, int $userId): string
    {
        return sprintf(CheckoutConst::RESERVED_TICKET_KEY, $ticketId, $userId);
    }

    /**
     * Increase the numbers of reserved tickets
     *
     * @param integer $userId
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function increaseReservedTickets(int $userId, array $numbersOfTickets): void
    {
        foreach ($numbersOfTickets as $ticketId => $numberOfTickets) {
            $key = self::getReservedTicketKey($ticketId, $userId);
            Redis::incrBy($key, $numberOfTickets);
            Redis::expire($key, CheckoutConst::RESERVED_TICKET_EXPIRATION);
        }
    }

    /**
     * Decrease the numbers of reserved tickets
     *
     * @param integer $userId
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseReservedTickets(int $userId, array $numbersOfTickets): void
    {
        foreach ($numbersOfTickets as $ticketId => $numberOfTickets) {
            $key = self::getReservedTicketKey($ticketId, $userId);
            Redis::decrBy($key, $numberOfTickets);
        }
    }
}
