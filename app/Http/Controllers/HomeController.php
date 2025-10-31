<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use DateTime;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $ticketRepository = new TicketRepository();

        return Inertia::render('Home', ['tickets' => $ticketRepository->selectPaginatedTickets(new DateTime('2000/01/01 00:00:00'))]);
    }
}
