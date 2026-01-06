<?php

namespace App\Services;

use App\Http\Resources\UserTicketResource;
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
     * @param Carbon|null $now
     * @return boolean
     */
    public static function isDuringSalesPeriod(Ticket $ticket, ?Carbon $now = null): bool
    {
        $now ??= new Carbon();
        return $now->between($ticket->start_date, $ticket->end_date);
    }

    /**
     * Check if ticket is during event
     *
     * @param Ticket $ticket
     * @param Carbon|null $now
     * @return void
     */
    public static function checkIfTicketIsDuringEvent(Ticket $ticket, ?Carbon $now = null): void
    {
        $now ??= new Carbon();
        if ($now->between($ticket->event_start_date, $ticket->event_end_date)) {
            return;
        }
        
        throw new RuntimeException("The ticket is outside the event period. ticket_id: {$ticket->id}");
    }

    /**
     * Check if event is not over
     *
     * @param Ticket $ticket
     * @param Carbon|null $now
     * @return void
     */
    public static function checkIfEventIsNotOver(Ticket $ticket, ?Carbon $now = null): void
    {
        $now ??= new Carbon();
        if ($now > $ticket->event_end_date) {
            throw new RuntimeException("The event is over. ticket_id: {$ticket->id}");
        }
    }

    /**
     * Check if the given numbers are not less than 0 or more than the numbers of tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function checkIfNumbersOfTicketsAreValid(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            self::checkIfNumberOfTicketsIsValid($ticket, $numbersOfTickets[$ticket->id]);
        }
    }

    /**
     * Check if the given number is not less than 0 or more than the number of tickets
     *
     * @param Ticket $ticket
     * @param integer $numberOfTickets
     * @return void
     */
    public static function checkIfNumberOfTicketsIsValid(Ticket $ticket, int $numberOfTickets): void
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
     * @param Carbon|null $now
     * @return boolean
     */
    public static function areEventAndTicketSalesDatesValid(Carbon $eventStartDate, Carbon $eventEndDate, Carbon $startDate, Carbon $endDate, Ticket $ticket = null, array &$errorMessage = [], ?Carbon $now = null): bool
    {
        if (!self::areEventAndTicketSalesDateOrdersValid($eventStartDate, $eventEndDate, $startDate, $endDate, $errorMessage)) {
            return false;
        }

        $now ??= new Carbon();
        if ($ticket !== null && TicketService::isDuringSalesPeriod($ticket, $now)) {
            return self::areEventAndTicketSalesDatesValidOnSale($startDate, $endDate, $ticket, $errorMessage, $now);
        }

        return self::areEventAndTicketSalesDatesValidBeforeSale($startDate, $errorMessage, $now);
    }

    /**
     * Whether date orders of event and ticket sales dates are valid
     *
     * @param Carbon $eventStartDate
     * @param Carbon $eventEndDate
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param array $errorMessage
     * @return boolean
     */
    private static function areEventAndTicketSalesDateOrdersValid(Carbon $eventStartDate, Carbon $eventEndDate, Carbon $startDate, Carbon $endDate, array &$errorMessage): bool
    {
        if ($endDate <= $startDate) {
            $errorMessage = ['end_date' => 'The ticket sales end date must be after the ticket sales start date.'];
            return false;
        }

        if ($eventStartDate <= $endDate) {
            $errorMessage = ['event_start_date' => 'The event start date must be after the ticket sales end date.'];
            return false;
        }

        if ($eventEndDate <= $eventStartDate) {
            $errorMessage = ['event_end_date' => 'The event end date must be after the event start date.'];
            return false;
        }

        return true;
    }

    /**
     * Whether event and sales dates for ticket on sale are valid
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param Ticket $ticket
     * @param array $errorMessage
     * @param Carbon $now
     * @return boolean
     */
    private static function areEventAndTicketSalesDatesValidOnSale(Carbon $startDate, Carbon $endDate, Ticket $ticket, array &$errorMessage, Carbon $now): bool
    {
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

    /**
     * Whether event and sales dates for ticket before sale are valid
     *
     * @param Carbon $startDate
     * @param array $errorMessage
     * @param Carbon $now
     * @return boolean
     */
    private static function areEventAndTicketSalesDatesValidBeforeSale(Carbon $startDate, array &$errorMessage, Carbon $now): bool
    {
        if ($now > $startDate) {
            $errorMessage = ['start_date' => 'The ticket sales start date must be in the future.'];
            return false;
        }

        return true;
    }

    /**
     * Whether the number of tickets is valid
     *
     * @param Ticket $ticket
     * @param integer $numberOfTickets
     * @param array $errorMessage
     * @return boolean
     */
    public static function isNumberOfTicketsValid(Ticket $ticket, int $numberOfTickets, array &$errorMessage = []): bool
    {
        if ($numberOfTickets < $ticket->number_of_tickets) {
            $errorMessage = ['number_of_tickets' => 'The number of tickets must be the current number or more.'];
            return false;
        }

        return true;
    }

    /**
     * Check if ticket is not used
     *
     * @param UserTicket $userTicket
     * @return void
     */
    public static function checkIfTicketIsNotUsed(UserTicket $userTicket): void
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
            
            $userTicketData[] = UserTicketResource::createUserTicketResource($userTicket, $ticket);
        }

        $paginator->setCollection(new Collection($userTicketData));
    }
}
