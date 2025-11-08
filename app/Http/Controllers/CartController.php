<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\UserCart;
use App\Repositories\TicketRepository;
use App\Repositories\UserCartRepository;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    /**
     * Store a ticket to cart
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required|integer',
            'number_of_tickets' => 'required|integer',
        ]);

        $userCartRepository = new UserCartRepository();
        $ticketRepository = new TicketRepository();

        $ticket = $ticketRepository->selectById($request->id) ?? throw ValidationException::withMessages(['id' => "Invalid ticket_id. ticket_id: {$request->id}"]);
        TicketService::checkIfNumberOfTicketsIsValid($request->number_of_tickets, $ticket);

        $user = $request->user();
        $userCart = $userCartRepository->selectByUserIdAndTicketId($user->id, $request->id) ?? new UserCart([
            'user_id' => $user->id,
            'ticket_id' => $request->id,
            'number_of_tickets' => 0,
        ]);

        $userCart->number_of_tickets += $request->number_of_tickets;

        $userCartRepository->save($userCart);

        return back();
    }

    /**
     * Show user carts
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $userCartRepository = new UserCartRepository();
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $userCarts = $userCartRepository->selectByUserId($user->id);

        $tickets = $ticketRepository->selectPaginatedTicketsByIds(array_column($userCarts->all(), 'ticket_id'));

        $numberOfTickets = array_column($userCarts->all(), 'number_of_tickets', 'ticket_id');

        return Inertia::render('Cart', ['tickets' => $tickets, 'numberOfTickets' => $numberOfTickets]);
    }

    /**
     * Update the number of tickets
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateNumberOfTickets(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'number_of_tickets' => 'required|integer',
        ]);

        $userCartRepository = new UserCartRepository();

        TicketService::checkIfNumberOfTicketsIsValid($request->number_of_tickets, $ticket);

        $user = $request->user();
        $userCart = $userCartRepository->selectByUserIdAndTicketId($user->id, $ticket->id) ?? throw new ValidationException("You don't have this ticket. ticket_id: {$ticket->id}");

        if ($userCart->number_of_tickets !== $request->number_of_tickets) {
            $userCart->number_of_tickets = $request->number_of_tickets;
        }

        $userCartRepository->save($userCart);

        return back();
    }
}
