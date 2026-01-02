<?php

namespace App\Http\Controllers;

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Repositories\TicketRepository;
use App\Services\CartService;
use App\Services\MoneyService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;

class CartController extends Controller
{
    /**
     * Store a ticket to cart
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'number_of_tickets' => ['required', 'integer'],
        ]);

        if (!TicketService::isDuringSalesPeriod($ticket)) {
            throw new InvalidArgumentException("The ticket is outside the sales period. ticket_id: {$ticket->id}");
        }

        TicketService::checkIfNumberOfTicketsIsValid($request->number_of_tickets, $ticket);

        $user = $request->user();
        CartService::increaseUserCart($user->id, $ticket->id, $request->number_of_tickets);

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
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $numbersOfTickets = CartService::getUserCarts($user->id);

        $paginator = $ticketRepository->selectPaginatedTicketsDuringSalesPeriodByIds(new Carbon, array_keys($numbersOfTickets));

        return Inertia::render('Cart', [
            'tickets' => TicketResource::collection($paginator),
            'numberOfTickets' => $numbersOfTickets,
            'totalPriceOfTickets' => MoneyService::convertCentsToDollars(CartService::getTotalPrice($paginator->getCollection(), $numbersOfTickets)),
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
            'number_of_tickets' => ['required', 'integer'],
        ]);

        $user = $request->user();
        if (!TicketService::isDuringSalesPeriod($ticket)) {
            CartService::deleteUserCart($user->id, $ticket->id);
            throw new InvalidArgumentException("The ticket is outside the sales period. ticket_id: {$ticket->id}");
        }

        TicketService::checkIfNumberOfTicketsIsValid($request->number_of_tickets, $ticket);

        $preNumberOfTickets = CartService::getUserCart($user->id, $ticket->id);

        if ($request->number_of_tickets !== $preNumberOfTickets) {
            CartService::increaseUserCart($user->id, $ticket->id, $request->number_of_tickets - $preNumberOfTickets);
        }

        return response()->json([
            'numberOfTickets' => $request->number_of_tickets,
            'differenceInTotalPrice' => MoneyService::convertCentsToDollars(CartService::getDifferenceInTotalPrice($preNumberOfTickets, $request->number_of_tickets, $ticket)),
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
        $user = $request->user();
        CartService::deleteUserCart($user->id, $ticket->id);

        return back();
    }
}
