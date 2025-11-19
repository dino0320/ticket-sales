<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use App\Repositories\UserCartRepository;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Cashier\Checkout;

class CheckoutController extends Controller
{
    /**
     * Show checkout
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

        return Inertia::render('Checkout', [
            'tickets' => $tickets,
            'numberOfTickets' => $numberOfTickets,
            'totalPriceOfTickets' => CartService::getTotalPrice($tickets->getCollection(), $numberOfTickets),
        ]);
    }

    /**
     * Checkout
     *
     * @param Request $request
     * @return Checkout
     */
    public function checkout(Request $request): Checkout
    {
        $stripePriceId = 'price_1SUGILHlSPswWVDqUjxrNIGe';
 
        $quantity = 1;
 
        return $request->user()->checkout([$stripePriceId => $quantity], [
            'success_url' => route('checkout-success'),
            'cancel_url' => route('checkout-cancel'),
        ]);
    }
}
