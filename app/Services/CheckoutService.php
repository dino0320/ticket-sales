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
     * @return void
     */
    public static function checkIfNumberOfTicketsIsValid(int $numberOfTickets, Ticket $ticket): void
    {
        if ($numberOfTickets <= 0 || $numberOfTickets > ($ticket->number_of_tickets - $ticket->number_of_reserved_tickets)) {
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
    public static function checkIfNumbersOfTicketsAreValid(array $numbersOfTickets, array $tickets): void
    {
        $tickets = array_column($tickets, null, 'id');
        foreach ($numbersOfTickets as $ticketId => $numberOfTickets) {
            self::checkIfNumberOfTicketsIsValid($numberOfTickets, $tickets[$ticketId]);
        }
    }

    /**
     * Increase the numbers of reserved tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function increaseNumbersOfReservedTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            $ticket->number_of_reserved_tickets += $numbersOfTickets[$ticket->id];
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
                throw new RuntimeException("The number of tickets is 0 or less. ticket_id: {$ticket->id}");
            }
            
            $ticket->number_of_tickets -= $numbersOfTickets[$ticket->id];
        }
    }

    /**
     * Decrease the numbers of reserved tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseNumbersOfReservedTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            if (($ticket->number_of_reserved_tickets - $numbersOfTickets[$ticket->id]) < 0) {
                throw new RuntimeException("The number of reserved tickets is less than 0. ticket_id: {$ticket->id}");
            }
            
            $ticket->number_of_reserved_tickets -= $numbersOfTickets[$ticket->id];
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
}
