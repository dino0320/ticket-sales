<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use App\Services\TicketService;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Show home
     *
     * @return Response
     */
    public function index(): Response
    {
        $ticketRepository = new TicketRepository();

        $tickets = $ticketRepository->selectPaginatedTicketsDuringPeriod(new Carbon());

        return Inertia::render('Home', [
            'tickets' => TicketService::getPaginatedTicketsResponse($tickets),
        ]);
    }
}
