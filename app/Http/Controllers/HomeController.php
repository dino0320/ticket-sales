<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use App\Services\PaginationService;
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

        $paginator = $ticketRepository->selectPaginatedTicketsDuringSalesPeriod(new Carbon());

        return Inertia::render('Home', [
            'tickets' => PaginationService::getPaginatedDataResponse($paginator, TicketService::getTicketsResponse($paginator->getCollection()->all())),
        ]);
    }
}
