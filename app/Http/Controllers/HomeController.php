<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Repositories\TicketRepository;
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

        $paginator = $ticketRepository->selectPaginatedTicketsDuringSalesPeriod(new Carbon());

        return Inertia::render('Home', [
            'tickets' => TicketResource::collection($paginator),
        ]);
    }
}
