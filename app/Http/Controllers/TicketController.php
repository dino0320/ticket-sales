<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    public function show(Ticket $ticket): Response
    {
        $now = new Carbon();
        if ($now <= $ticket->start_date || $now >= $ticket->end_date) {
            throw new NotFoundHttpException('The event is outside the specified time period.');
        }

        return Inertia::render('TicketDetail', ['ticket' => $ticket]);
    }
}
