<?php

namespace App\Http\Controllers;

use App\Consts\AccountConst;
use App\Consts\TicketConst;
use App\Models\User;
use App\Models\UserOrganizerApplication;
use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserOrganizerApplicationRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserTicketRepository;
use App\Services\OrderHistoryService;
use App\Services\OrganizerService;
use App\Services\PaginationService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AccountController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'name' => ['required', 'string', 'max:' . AccountConst::NAME_LENGTH_MAX],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:' . AccountConst::EMAIL_LENGTH_MAX, 'unique:' . User::class],
                'password' => [
                    'required',
                    'confirmed',
                    Password::defaults()->min(AccountConst::PASSWORD_LENGTH_MIN)->max(AccountConst::PASSWORD_LENGTH_MAX)->mixedCase()->numbers()->symbols(),
                ],
                'password_confirmation' => ['required'],
            ]);

            $userRepository = new UserRepository();

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'is_organizer' => false,
            ]);
        
            $userRepository->save($user);

            event(new Registered($user));

            Auth::login($user);

            $request->session()->regenerate();

            return redirect()->intended('/home');
        });
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'max:' . AccountConst::EMAIL_LENGTH_MAX],
            'password' => ['required', 'string', 'max:' . AccountConst::PASSWORD_LENGTH_MAX],
        ]);
 
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
 
            return redirect()->intended('/home');
        }
 
        return back()->withErrors([
            'root' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Show user account
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $userOrganizerApplicationRepository = new UserOrganizerApplicationRepository();
        $userTicketRepository = new UserTicketRepository();
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $userTickets = $userTicketRepository->selectNotUsedTicketsByUserId($user->id);
        $paginator = $ticketRepository->selectPaginatedTicketsDuringEventByIds(array_column($userTickets, 'ticket_id'), new Carbon());

        $isOrganizerApplicationApplied = true;
        if (!$user->is_organizer) {
            $userOrganizerApplication = $userOrganizerApplicationRepository->selectByUserId($user->id);
            $isOrganizerApplicationApplied = OrganizerService::isOrganizerApplicationApplied($userOrganizerApplication);
        }

        return Inertia::render('Account', [
            'tickets' => PaginationService::getPaginatedDataResponse($paginator, TicketService::getTicketsResponse($paginator->getCollection()->all())),
            'isOrganizerApplicationApplied' => $isOrganizerApplicationApplied,
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
        $paginator = $userOrderRepository->selectPaginatedUserOrdersByUserId($user->id);

        return Inertia::render('OrderHistory', [
            'userOrders' => PaginationService::getPaginatedDataResponse($paginator, OrderHistoryService::getUserOrdersResponse($paginator->getCollection()->all())),
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
                'email' => ['required', 'string', 'max:' . AccountConst::EMAIL_LENGTH_MAX],
                'password' => ['required', 'string', 'max:' . AccountConst::PASSWORD_LENGTH_MAX],
                'new_password' => [
                    'required',
                    'confirmed',
                    Password::defaults()->min(AccountConst::PASSWORD_LENGTH_MIN)->max(AccountConst::PASSWORD_LENGTH_MAX)->mixedCase()->numbers()->symbols(),
                ],
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
                'root' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        });
    }

    /**
     * Apply to be an organizer
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function applyToBeOrganizer(Request $request): RedirectResponse
    {
        return DB::transaction(function () use ($request) {
            $request->validate([
                'event_description' => ['required', 'string', 'max:' . TicketConst::EVENT_DESCRIPTION_LENGTH_MAX],
                'is_individual' => ['required', 'boolean'],
                'website_url' => ['nullable', 'url:http,https', 'max:' . AccountConst::URL_LENGTH_MAX],
            ]);

            $userRepository = new UserRepository();
            $userOrganizerApplicationRepository = new UserOrganizerApplicationRepository();

            $user = $request->user();
            $userOrganizerApplication = $userOrganizerApplicationRepository->selectByUserId($user->id) ?? new UserOrganizerApplication([
                'user_id' => $user->id,
                'status' => AccountConst::ORGANIZER_STATUS_UNAPPROVED,
            ]);
            if ($userOrganizerApplication->status !== AccountConst::ORGANIZER_STATUS_UNAPPROVED) {
                return back()->withErrors([
                    'root' => 'You already applied for this.',
                ]);
            }

            $userOrganizerApplication->status = AccountConst::ORGANIZER_STATUS_PENDING;
            $userOrganizerApplication->event_description = $request->event_description;
            $userOrganizerApplication->is_individual = $request->is_individual;
            $userOrganizerApplication->website_url = $request->website_url;
            $userOrganizerApplication->applied_at = new Carbon();

            $userRepository->save($user);
            $userOrganizerApplicationRepository->save($userOrganizerApplication);
 
            return redirect()->intended('/my-account');
        });
    }

    /**
     * Show issued tickets
     *
     * @param Request $request
     * @return Response
     */
    public function showIssuedTickets(Request $request): Response
    {
        $ticketRepository = new TicketRepository();

        $user = $request->user();
        $paginator = $ticketRepository->selectPaginatedTicketsByOrganizerUserId($user->id);

        return Inertia::render('IssuedTicketIndex', [
            'tickets' => PaginationService::getPaginatedDataResponse($paginator, TicketService::getTicketsResponse($paginator->getCollection()->all())),
        ]);
    }
}
