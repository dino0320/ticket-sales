<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $now = '2000-04-27 00:00:00'; //now();
        $tickets = Ticket::select([
                'id',
                'event_title',
                'event_description',
                'price',
                'event_start_date',
                'event_end_date',
                'start_date',
                'end_date'
            ])->where([
                ['start_date', '>=', $now],
                ['end_date', '<=', $now],
            ])->orderBy('event_start_date', 'asc')->paginate(3);

        return Inertia::render('Home', ['tickets' => $tickets]);
    }
}
