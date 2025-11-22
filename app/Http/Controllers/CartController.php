<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\UserCart;
use App\Repositories\TicketRepository;
use App\Repositories\UserCartRepository;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
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
        CartService::checkIfNumberOfTicketsIsValid($request->number_of_tickets, $ticket);

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

        $tickets = $ticketRepository->selectPaginatedTicketsByIds(array_column($userCarts, 'ticket_id'));

        $numberOfTickets = CartService::getNumberOfTickets($userCarts);

        return Inertia::render('Cart', [
            'tickets' => $tickets,
            'numberOfTickets' => $numberOfTickets,
            'totalPriceOfTickets' => CartService::getTotalPrice($tickets->getCollection(), $numberOfTickets),
        ]);
    }

    /**
     * Update a ticket in cart
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function update(Request $request, Ticket $ticket): JsonResponse
    {
        $request->validate([
            'number_of_tickets' => 'required|integer',
        ]);

        $userCartRepository = new UserCartRepository();

        CartService::checkIfNumberOfTicketsIsValid($request->number_of_tickets, $ticket);

        $user = $request->user();
        $userCart = $userCartRepository->selectByUserIdAndTicketId($user->id, $ticket->id) ?? throw new ValidationException("You don't have this ticket. ticket_id: {$ticket->id}");
        $preNumberOfTickets = $userCart->number_of_tickets;

        if ($request->number_of_tickets !== $preNumberOfTickets) {
            $userCart->number_of_tickets = $request->number_of_tickets;
        }

        $userCartRepository->save($userCart);

        return response()->json([
            'numberOfTickets' => $userCart->number_of_tickets,
            'differenceInTotalPrice' => CartService::getDifferenceInTotalPrice($preNumberOfTickets, $request->number_of_tickets, $ticket),
        ]);
    }

    /**
     * Delete a ticket from cart
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function destroy(Request $request, Ticket $ticket): RedirectResponse
    {
        $userCartRepository = new UserCartRepository();

        $user = $request->user();
        $userCart = $userCartRepository->selectByUserIdAndTicketId($user->id, $ticket->id) ?? throw new ValidationException("You don't have this ticket. ticket_id: {$ticket->id}");
        
        $userCartRepository->delete($userCart);

        return back();
    }
}
