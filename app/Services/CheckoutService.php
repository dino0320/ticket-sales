<?php

namespace App\Services;

use App\Consts\CheckoutConst;
use App\Models\Ticket;
use App\Models\UserOrder;
use App\Models\UserTicket;
use Illuminate\Support\Facades\Redis;
use InvalidArgumentException;
use RuntimeException;

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
     * Decrease the numbers of tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseNumbersOfTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            if (($ticket->number_of_tickets - $numbersOfTickets[$ticket->id]) <= 0) {
                throw new RuntimeException("The number of tickets is less than 0. ticket_id: {$ticket->id}");
            }
            
            $ticket->number_of_tickets -= $numbersOfTickets[$ticket->id];
        }
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
     * Get a reserved ticket key
     *
     * @return string
     */
    private static function getReservedTicketKey(): string
    {
        return CheckoutConst::RESERVED_TICKET_KEY;
    }

    /**
     * Get the number of reserved tickets
     *
     * @param integer $ticketId
     * @return int
     */
    public static function getReservedTicket(int $ticketId): int
    {
        return Redis::hGet(self::getReservedTicketKey(), $ticketId) ?? throw new InvalidArgumentException("Can't get the number of reserved tickets. ticket_id: {$ticketId}");
    }

    /**
     * Get the numbers of reserved tickets
     *
     * @param int[] $ticketIds
     * @return int[]
     */
    public static function getReservedTickets(array $ticketIds): array
    {
        return array_combine($ticketIds, Redis::hMGet(self::getReservedTicketKey(), $ticketIds));
    }

    /**
     * Increase the number of reserved tickets
     *
     * @param integer $ticketId
     * @param integer $numberOfTickets
     * @return void
     */
    private static function increaseReservedTicket(int $ticketId, int $numberOfTickets): void
    {
        Redis::hIncrBy(self::getReservedTicketKey(), $ticketId, $numberOfTickets);
    }

    /**
     * Increase the numbers of reserved tickets
     *
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function increaseReservedTickets(array $numbersOfTickets): void
    {
        foreach ($numbersOfTickets as $tticketId => $numberOfTickets) {
            self::increaseReservedTicket($tticketId, $numberOfTickets);
        }
    }

    /**
     * Decrease the numbers of reserved tickets
     *
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseReservedTickets(array $numbersOfTickets): void
    {
        foreach ($numbersOfTickets as $tticketId => $numberOfTickets) {
            self::increaseReservedTicket($tticketId, -$numberOfTickets);
        }
    }
}
