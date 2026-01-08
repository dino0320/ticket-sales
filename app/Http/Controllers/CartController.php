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
     * Show user carts
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $ticketRepository = new TicketRepository();

        $numbersOfTickets = CartService::getCart(CartService::getCartId($request->user()));

        $paginator = $ticketRepository->selectPaginatedTicketsDuringSalesPeriodByIds(new Carbon, array_keys($numbersOfTickets));

        return Inertia::render('Cart', [
            'tickets' => TicketResource::collection($paginator),
            'numberOfTickets' => $numbersOfTickets,
            'totalPriceOfTickets' => MoneyService::convertCentsToDollars(CartService::getTotalPrice($paginator->getCollection(), $numbersOfTickets)),
        ]);
    }

    /**
     * Store ticket to cart
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
            return back()->withErrors(['sales_period' => 'The ticket is outside the sales period.']);
        }

        $cartId = CartService::getCartId($request->user());
        $numberOfTickets = CartService::getNumberOfTicketsFromCart($cartId, $ticket->id) + $request->number_of_tickets;
        if (!TicketService::isNumberOfTicketsValid($ticket, $numberOfTickets)) {
            return back()->withErrors(['number_of_tickets' => 'The number of tickets is invalid.']);
        }

        CartService::increaseNumberOfTicketsInCart($cartId, $ticket->id, $request->number_of_tickets);

        return back();
    }

    /**
     * Update ticket in cart
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

        $cartId = CartService::getCartId($request->user());
        if (!TicketService::isDuringSalesPeriod($ticket)) {
            CartService::deleteTicketInCart($cartId, $ticket->id);
            return response()->json(['sales_period' => 'The ticket is outside the sales period.'], HttpFoundationResponse::HTTP_BAD_REQUEST);
        }

        if (!TicketService::isNumberOfTicketsValid($ticket, $request->number_of_tickets)) {
            return response()->json(['number_of_tickets' => 'The number of tickets is invalid.'], HttpFoundationResponse::HTTP_BAD_REQUEST);
        }

        $preNumberOfTickets = CartService::getNumberOfTicketsFromCart($cartId, $ticket->id);

        if ($request->number_of_tickets !== $preNumberOfTickets) {
            CartService::increaseNumberOfTicketsInCart($cartId, $ticket->id, $request->number_of_tickets - $preNumberOfTickets);
        }

        return response()->json([
            'numberOfTickets' => $request->number_of_tickets,
            'differenceInTotalPrice' => MoneyService::convertCentsToDollars(CartService::getDifferenceInTotalPrice($request->number_of_tickets, $preNumberOfTickets, $ticket)),
        ]);
    }

    /**
     * Delete ticket from cart
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return JsonResponse
     */
    public function destroy(Request $request, Ticket $ticket): RedirectResponse
    {
        CartService::deleteTicketInCart(CartService::getCartId($request->user()), $ticket->id);

        return back();
    }
}
