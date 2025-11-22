<?php

namespace App\Http\Controllers;

use App\Consts\CheckoutConst;
use App\Models\UserOrder;
use App\Repositories\TicketRepository;
use App\Repositories\UserCartRepository;
use App\Repositories\UserOrderRepository;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use InvalidArgumentException;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Checkout;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;

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

        $tickets = $ticketRepository->selectPaginatedTicketsByIds(array_column($userCarts, 'ticket_id'));

        $numberOfTickets = array_column($userCarts, 'number_of_tickets', 'ticket_id');

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
        $userCartRepository = new UserCartRepository();
        $userOrderRepository = new UserOrderRepository();
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $userCarts = $userCartRepository->selectByUserId($user->id);
        $tickets = $ticketRepository->selectByIds(array_column($userCarts, 'ticket_id'));
        
        $userOrder = new UserOrder([
            'user_id' => $user->id,
            'order_items' => CheckoutService::getOrderItems($userCarts, $tickets),
            'status' => CheckoutConst::ORDER_STATUS_INCOMPLETE,
        ]);

        $userOrderRepository->save($userOrder);
        
        return $user->checkout(CheckoutService::getStripePriceIds($userOrder), [
            'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('review'),
            'metadata' => ['user_order_id' => $userOrder->id],
            'payment_intent_data' => [
                'metadata' => ['user_order_id' => $userOrder->id],
            ],
        ]);
    }

    /**
     * Show checkout success
     *
     * @param Request $request
     * @return Response
     */
    public function showCheckoutSuccess(Request $request): Response
    {
        $userOrderRepository = new UserOrderRepository();

        $sessionId = $request->get('session_id') ?? throw new SessionNotFoundException('The Session ID doesn\'t exist.');
 
        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
 
        if ($session->payment_status !== 'paid') {
            throw new InvalidArgumentException('The order hasn\'t paid.');
        }
        
        $userOrderId = $session['metadata']['user_order_id'] ?? throw new InvalidArgumentException('The order ID is missing.');;
        
        $userOrder = $userOrderRepository->selectById($userOrderId);

        return Inertia::render('CheckoutSuccess', [
            'userOrderId' => $userOrder->id,
        ]);
    }
}
