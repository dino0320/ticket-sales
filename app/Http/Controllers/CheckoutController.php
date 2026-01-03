<?php

namespace App\Http\Controllers;

use App\Consts\CheckoutConst;
use App\Http\Resources\TicketResource;
use App\Models\UserOrder;
use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\MoneyService;
use App\Services\StripeService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $numbersOfTickets = CartService::getCart($user->id);

        $paginator = $ticketRepository->selectPaginatedTicketsDuringSalesPeriodByIds(new Carbon(), array_keys($numbersOfTickets));

        return Inertia::render('Review', [
            'tickets' => TicketResource::collection($paginator),
            'numberOfTickets' => $numbersOfTickets,
            'totalPriceOfTickets' => MoneyService::convertCentsToDollars(CartService::getTotalPrice($paginator->getCollection(), $numbersOfTickets)),
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

        $sessionId = $request->get('session_id') ?? throw new SessionNotFoundException('The Session ID doesn\'t exist');
 
        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
 
        if ($session->payment_status !== 'paid') {
            throw new InvalidArgumentException('The order hasn\'t paid');
        }
        
        $userOrderId = $session['metadata']['user_order_id'] ?? throw new InvalidArgumentException('The order ID is missing');
        
        $userOrder = $userOrderRepository->selectById($userOrderId);

        return Inertia::render('CheckoutSuccess', [
            'userOrderId' => $userOrder->id,
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
        $userOrder = DB::transaction(function () use ($request) {
            $userOrderRepository = new UserOrderRepository();
            $ticketRepository = new TicketRepository();

            $user = $request->user();
            $numbersOfTickets = CartService::getCart($user->id);
            if ($numbersOfTickets === []) {
                throw new InvalidArgumentException("No items in the cart. user_id: {$user->id}");
            }

            $tickets = $ticketRepository->selectTicketsDuringSalesPeriodByIdsForUpdate(new Carbon(), array_keys($numbersOfTickets));
            if ($tickets === []) {
                CartService::deleteCart($user->id);
                throw new InvalidArgumentException("No valid tickets in the cart. user_id: {$user->id}");
            }

            TicketService::checkIfNumbersOfTicketsAreValid($numbersOfTickets, $tickets);

            CheckoutService::increaseNumbersOfReservedTickets($tickets, $numbersOfTickets);
        
            $userOrder = new UserOrder([
                'user_id' => $user->id,
                'amount' => 0,
                'order_items' => CheckoutService::createOrderItems($tickets, $numbersOfTickets),
                'status' => CheckoutConst::ORDER_STATUS_PENDING,
            ]);

            $userOrderRepository->save($userOrder);
            $ticketRepository->upsert($tickets);

            return $userOrder;
        });
        

        // Invoke external APIs outside the transaction to prevent long-term DB locks
        return StripeService::checkout($request->user(), $userOrder);
    }
}
