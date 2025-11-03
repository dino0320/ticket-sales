<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $ticketRepository = new TicketRepository();

        return Inertia::render('Home', ['tickets' => $ticketRepository->selectPaginatedTickets(new Carbon())]);
    }
}
