<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\UserCart;
use App\Repositories\UserCartRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserCartController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id' => 'required|exists:' . Ticket::class,
            'number_of_tickets' => 'required|integer',
        ]);

        $userCartController = new UserCartRepository();

        if ($request->number_of_tickets <= 0) {
            throw new ValidationException("Invalid number_of_tickets. number_of_tickets: {$request->number_of_tickets}");
        }

        $user = $request->user();
        $userCart = $userCartController->selectByUserIdAndTicketId($user->id, $request->id) ?? new UserCart([
            'user_id' => $user->id,
            'ticket_id' => $request->id,
            'number_of_tickets' => 0,
        ]);

        $userCart->number_of_tickets += $request->number_of_tickets;

        $userCartController->save($userCart);

        return back();
    }
}
