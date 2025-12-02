<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
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
        $now = new Carbon();
        if ($now <= $ticket->start_date || $now >= $ticket->end_date) {
            throw new NotFoundHttpException('The event is outside the specified time period.');
        }

        return Inertia::render('TicketDetail', [
            'ticket' => TicketService::getTicketResponse($ticket),
        ]);
    }
}
