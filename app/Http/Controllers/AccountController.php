<?php

namespace App\Http\Controllers;

use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTicketRepository;
use App\Services\OrderHistoryService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

    /**
     * Reset password
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $credentials = $request->validate([
                'email' => 'required|string|lowercase|email|max:255',
                'password' => ['required', Password::defaults()],
                'new_password' => ['required', 'confirmed', Password::defaults()],
                'new_password_confirmation' => ['required'],
            ]);

            $userRepository = new UserRepository();

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = $request->user();
                $user->password = $request->new_password;

                $userRepository->save($user);
 
                return redirect()->intended('/my-account');
            }
 
            return back()->withErrors([
                'other' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        });
    }
}
