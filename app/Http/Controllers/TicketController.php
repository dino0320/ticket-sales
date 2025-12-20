<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    /**
     * Show a ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function show(Ticket $ticket): Response
    {
        if (!TicketService::isDuringPeriod($ticket)) {
            throw new NotFoundHttpException('The event is outside the specified time period.');
        }

        return Inertia::render('TicketDetail', [
            'ticket' => TicketService::getTicketResponse($ticket),
        ]);
    }

    /**
     * Show an issued ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function showIssuedTicket(Request $request, Ticket $ticket): Response
    {
        $user = $request->user();
        if ($user->id !== $ticket->organizer_user_id) {
            throw new InvalidArgumentException("The User ID is not the organizer's User ID of this ticket. user_id: {$user->id}, ticket_id: {$ticket->id}");
        }

        return Inertia::render('EditIssuedTicket', [
            'ticket' => TicketService::getIssuedTicketResponse($ticket),
        ]);
    }

    /**
     * Update an issued ticket
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return RedirectResponse
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        return DB::transaction(function () use ($request, $ticket) {
            $request->validate([
                'event_title' => 'required|string|max:255',
                'event_description' => 'nullable|string|max:255',
                'number_of_tickets' => 'required|integer|min:1',
                'event_start_date' => 'required|date',
                'event_end_date' => 'nullable|date',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $ticketRepository = new TicketRepository();

            $startDate = $request->date('start_date');
            $endDate = $request->date('end_date');
            $eventStartDate = $request->date('event_start_date');
            $eventEndDate = $request->date('event_end_date');

            if ($request->number_of_tickets < $ticket->number_of_tickets) {
                return back()->withErrors([
                    'number_of_tickets' => 'The number of tickets must be the previous value or more.',
                ]);
            }

            if ($eventStartDate <= $endDate) {
                return back()->withErrors([
                    'event_start_date' => 'The event start date must be after the ticket sales end date.',
                ]);
            }

            if ($eventEndDate <= $eventStartDate) {
                return back()->withErrors([
                    'event_end_date' => 'The event end date must be after the event start date.',
                ]);
            }

            $now = new Carbon();
            if (TicketService::isDuringPeriod($ticket)) {
                if ($now > $endDate) {
                    return back()->withErrors([
                        'end_date' => 'The ticket sales end date must be in the future.',
                    ]);
                }

                $ticket->end_date = $endDate;
                $ticket->event_start_date = $eventStartDate;
                $ticket->event_end_date = $eventEndDate;

                $ticketRepository->save($ticket);

                return redirect()->intended('/issued_tickets');
            }

            // Not during the period

            if ($now > $startDate) {
                return back()->withErrors([
                    'start_date' => 'The ticket sales start date must be in the future.',
                ]);
            }

            if ($endDate <= $startDate) {
                return back()->withErrors([
                    'end_date' => 'The ticket sales end date must be after the ticket sales start date.',
                ]);
            }

            $ticket->start_date = $startDate;
            $ticket->end_date = $endDate;
            $ticket->event_start_date = $eventStartDate;
            $ticket->event_end_date = $eventEndDate;

            $ticketRepository->save($ticket);
            
            return redirect()->intended('/issued_tickets');
        });
    }
}
