<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTicket;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;
use InvalidArgumentException;

class TicketService
{
    /**
     * Get paginated tickets response
     *
     * @param CursorPaginator $tickets
     * @return array
     */
    public static function getPaginatedTicketsResponse(CursorPaginator $tickets): array
    {
        $ticketsResponse = [
            'data' => self::getTicketsResponse($tickets->getCollection()->all()),
            'prev_page_url' => $tickets->previousPageUrl(),
            'next_page_url' => $tickets->nextPageUrl(),
        ];

        return $ticketsResponse;
    }

    /**
     * Get tickets response
     *
     * @param Ticket[] $tickets
     * @return array
     */
    private static function getTicketsResponse(array $tickets): array
    {
        $ticketsResponse = [];
        foreach ($tickets as $ticket) {
            $ticketsResponse[] = self::getTicketResponse($ticket);
        }

        return $ticketsResponse;
    }

    /**
     * Get ticket response
     *
     * @param Ticket $ticket
     * @return array
     */
    public static function getTicketResponse(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'event_title' => $ticket->event_title,
            'event_description' => $ticket->event_description,
            'price' => $ticket->price,
            'event_start_date' => $ticket->event_start_date,
            'event_end_date' => $ticket->event_end_date,
        ];
    }

    /**
     * Get issued ticket response
     *
     * @param Ticket $ticket
     * @return array
     */
    public static function getIssuedTicketResponse(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'event_title' => $ticket->event_title,
            'event_description' => $ticket->event_description,
            'price' => $ticket->price,
            'number_of_tickets' => $ticket->number_of_tickets,
            'event_start_date' => $ticket->event_start_date,
            'event_end_date' => $ticket->event_end_date,
            'start_date' => $ticket->start_date,
            'end_date' => $ticket->end_date,
        ];
    }

    /**
     * Wether a ticket is during the period
     *
     * @param Ticket $ticket
     * @return boolean
     */
    public static function isDuringPeriod(Ticket $ticket): bool
    {
        $now = new Carbon();
        return $now >= $ticket->start_date && $now <= $ticket->end_date;
    }

    /**
     * Wether a ticket is during the event
     *
     * @param Ticket $ticket
     * @return boolean
     */
    public static function isDuringEvent(Ticket $ticket): bool
    {
        $now = new Carbon();
        return $now >= $ticket->event_start_date && $now <= $ticket->event_end_date;
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
     * Wether the event and ticket sales dates are valid
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
        if ($ticket !== null && TicketService::isDuringPeriod($ticket)) {
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
}
