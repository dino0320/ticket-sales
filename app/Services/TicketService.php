<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTicket;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RuntimeException;

class TicketService
{
    /**
     * Whether ticket is during sales period
     *
     * @param Ticket $ticket
     * @return boolean
     */
    public static function isDuringSalesPeriod(Ticket $ticket): bool
    {
        $now = new Carbon();
        return $now >= $ticket->start_date && $now <= $ticket->end_date;
    }

    /**
     * Check if ticket is during event
     *
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfTicketIsDuringEvent(Ticket $ticket): void
    {
        $now = new Carbon();
        if ($now < $ticket->event_start_date || $now > $ticket->event_end_date) {
            throw new RuntimeException("The ticket is outside the event period. ticket_id: {$ticket->id}");
        }
    }

    /**
     * Check if event is over
     *
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfEventIsOver(Ticket $ticket): void
    {
        $now = new Carbon();
        if ($now > $ticket->event_end_date) {
            throw new RuntimeException("The event is over. ticket_id: {$ticket->id}");
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
        foreach ($tickets as $ticket) {
            self::checkIfNumberOfTicketsIsValid($numbersOfTickets[$ticket->id], $ticket);
        }
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
        if ($numberOfTickets <= 0 || $numberOfTickets > ($ticket->number_of_tickets - $ticket->number_of_reserved_tickets)) {
            $estimatedNumberOfTickets = $ticket->number_of_tickets - $ticket->number_of_reserved_tickets;
            throw new RuntimeException("The number of tickets is invalid. ticket_id: {$ticket->id}, number_of_tickets: {$estimatedNumberOfTickets}, used_number_of_tickets: {$numberOfTickets}");
        }
    }

    /**
     * Check if user is organizer of ticket
     *
     * @param User $user
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfUserIsOrganizerOfTicket(User $user, Ticket $ticket): void
    {
        OrganizerService::checkIfUserIsOrganizer($user);

        if ($user->id === $ticket->organizer_user_id) {
            return;
        }

        throw new RuntimeException("The user is not an organizer of the ticket. user_id: {$user->id}, ticket_id: {$ticket->organizer_user_id}");
    }

    /**
     * Whether event and ticket sales dates are valid
     *
     * @param Carbon $eventStartDate
     * @param Carbon $eventEndDate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Ticket|null $ticket
     * @param array $errorMessage
     * @return boolean
     */
    public static function areEventAndTicketSalesDatesValid(Carbon $eventStartDate, Carbon $eventEndDate, Carbon $startDate, Carbon $endDate, Ticket $ticket = null, array &$errorMessage = []): bool
    {
        if ($eventStartDate <= $endDate) {
            $errorMessage = ['event_start_date' => 'The event start date must be after the ticket sales end date.'];
            return false;
        }

        if ($eventEndDate <= $eventStartDate) {
            $errorMessage = ['event_end_date' => 'The event end date must be after the event start date.'];
            return false;
        }

        $now = new Carbon();
        if ($ticket !== null && TicketService::isDuringSalesPeriod($ticket)) {
            if (!$startDate->equalTo($ticket->start_date)) {
                $errorMessage = ['start_date' => 'The ticket sales start date cannot be changed.'];
                return false;
            }

            if ($now > $endDate) {
                $errorMessage = ['end_date' => 'The ticket sales end date must be in the future.'];
                return false;
            }

            return true;
        }

        // Not during sales period

        if ($now > $startDate) {
            $errorMessage = ['start_date' => 'The ticket sales start date must be in the future.'];
            return false;
        }

        if ($endDate <= $startDate) {
            $errorMessage = ['end_date' => 'The ticket sales end date must be after the ticket sales start date.'];
            return false;
        }

        return true;
    }

    /**
     * Whether the number of tickets is valid
     *
     * @param Carbon $eventStartDate
     * @param Carbon $eventEndDate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Ticket|null $ticket
     * @param array $errorMessage
     * @return boolean
     */
    public static function isNumberOfTicketsValid(int $numberOfTickets, Ticket $ticket = null, array &$errorMessage = []): bool
    {
        if ($numberOfTickets < $ticket->number_of_tickets) {
            $errorMessage = ['number_of_tickets' => 'The number of tickets must be the current number or more.'];
            return false;
        }

        return true;
    }

    /**
     * Check if ticket is used
     *
     * @param UserTicket $userTicket
     * @return void
     */
    public static function checkIfTicketIsUsed(UserTicket $userTicket): void
    {
        if ($userTicket->used_at !== null) {
            throw new RuntimeException("The ticket has already been used. user_ticket_id: {$userTicket->id}");
        }
    }

    /**
     * Update user ticket data in paginator
     *
     * @param LengthAwarePaginator $paginator
     * @param Ticket[] $tickets
     * @return void
     */
    public static function updateUserTicketDataInPaginator(LengthAwarePaginator $paginator, array $tickets): void
    {
        $tickets = array_column($tickets, null, 'id');

        $userTicketData = [];
        foreach ($paginator->getCollection() as $userTicket) {
            $ticket = $tickets[$userTicket->ticket_id] ?? null;
            if ($ticket === null) {
                continue;
            }
            
            $userTicketData[] = [
                'id' => $userTicket->id,
                'event_title' => $ticket->event_title,
                'event_description' => $ticket->event_description,
                'price' => MoneyService::convertCentsToDollars($ticket->price),
                'event_start_date' => $ticket->event_start_date,
                'event_end_date' => $ticket->event_end_date,
            ];
        }

        $paginator->setCollection(new Collection($userTicketData));
    }
}
