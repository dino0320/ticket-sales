<?php

namespace App\Services;

use App\Consts\TicketConst;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTicket;
use Carbon\Carbon;
use InvalidArgumentException;

class TicketService
{
    /**
     * Whether a ticket is during the sales period
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
     * Check if the event is over
     *
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfEventIsOver(Ticket $ticket): void
    {
        $now = new Carbon();
        if ($now > $ticket->event_end_date) {
            throw new InvalidArgumentException("The event is over. ticket_id: {$ticket->id}");
        }
    }

    /**
     * Check if the user is the organizer for a ticket
     *
     * @param User $user
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfUserIsOrganizerForTicket(User $user, Ticket $ticket): void
    {
        OrganizerService::checkIfUserIsOrganizer($user);

        if ($user->id === $ticket->organizer_user_id) {
            return;
        }

        throw new InvalidArgumentException("The User ID is not the organizer's User ID of this ticket. user_id: {$user->id}, ticket_id: {$ticket->organizer_user_id}");
    }

    /**
     * Whether the event and ticket sales dates are valid
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

        // Not during the period

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
     * Whether the number of tickets can be updated
     *
     * @param integer $numberOfTickets
     * @param Ticket $ticket
     * @param array $errorMessage
     * @return boolean
     */
    public static function canUpdateNumberOfTickets(int $numberOfTickets, Ticket $ticket, array &$errorMessage = []): bool
    {
        if ($numberOfTickets === $ticket->initial_number_of_tickets) {
            return true;
        }

        if ($numberOfTickets < TicketConst::NUMBER_OF_TICKETS_MIN || $numberOfTickets > TicketConst::NUMBER_OF_TICKETS_MAX) {
            $errorMessage = ['number_of_tickets' => sprintf('The number of tickets must be more than %1$d and less than %2$d.', TicketConst::NUMBER_OF_TICKETS_MIN, TicketConst::NUMBER_OF_TICKETS_MAX)];
            return false;
        }

        if (TicketService::isDuringSalesPeriod($ticket)) {
            $errorMessage = ['number_of_tickets' => 'The number of tickets cannot be changed.'];
            return false;
        }

        return true;
    }

    /**
     * Check if the ticket is used
     *
     * @param UserTicket $userTicket
     * @return void
     */
    public static function checkIfTicketIsUsed(UserTicket $userTicket): void
    {
        if ($userTicket->used_at !== null) {
            throw new InvalidArgumentException("The ticket has already been used. user_ticket_id: {$userTicket->id}");
        }
    }

    /**
     * Check if the ticket is during the event
     *
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfTicketIsDuringEvent(Ticket $ticket): void
    {
        $now = new Carbon();
        if ($now < $ticket->event_start_date || $now > $ticket->event_end_date) {
            throw new InvalidArgumentException("The ticket is outside the specified time period. ticket_id: {$ticket->id}");
        }
    }
}
