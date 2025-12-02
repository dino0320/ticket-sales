<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserTicketRepository;
use App\Services\OrderHistoryService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    /**
     * Show user account
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $userTicketRepository = new UserTicketRepository();
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $userTickets = $userTicketRepository->selectByUserId($user->id);
        $tickets = $ticketRepository->selectPaginatedTicketsDuringEventByIds(array_column($userTickets, 'ticket_id'), new Carbon('2000/01/01 00:00:00'));

        return Inertia::render('Account', [
            'tickets' => TicketService::getPaginatedTicketsResponse($tickets),
        ]);
    }

    /**
     * Show order history
     *
     * @param Request $request
     * @return Response
     */
    public function showOrderHistory(Request $request): Response
    {
        $userOrderRepository = new UserOrderRepository();

        $user = $request->user();
        $userOrders = $userOrderRepository->selectPaginatedUserOrdersByUserId($user->id);

        return Inertia::render('OrderHistory', [
            'userOrders' => OrderHistoryService::getPaginatedUserOrdersResponse($userOrders),
        ]);
    }
}
