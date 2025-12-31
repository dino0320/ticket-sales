<?php

namespace App\Http\Controllers;

use App\Consts\TicketConst;
use App\Models\Ticket;
use App\Models\UserTicket;
use App\Repositories\TicketRepository;
use App\Repositories\UserTicketRepository;
use App\Services\MoneyService;
use App\Services\OrganizerService;
use App\Services\StripeService;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    /**
     * Show a ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function show(Ticket $ticket): Response
    {
        if (!TicketService::isDuringPeriod($ticket)) {
            throw new NotFoundHttpException('The ticket is outside the specified time period.');
        }

        return Inertia::render('TicketDetail', [
            'ticket' => TicketService::getTicketResponse($ticket),
        ]);
    }

    /**
     * Show a purchased ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function showUserTicket(Request $request, Ticket $ticket): Response
    {
        TicketService::checkIfEventIsOver($ticket);

        $userTicketRepository = new UserTicketRepository();

        $user = $request->user();
        $userTicket = $userTicketRepository->selectByUserIdAndTicketId($user->id, $ticket->id);

        TicketService::checkIfTicketIsUsed($userTicket);

        return Inertia::render('UserTicketDetail', [
            'ticket' => TicketService::getTicketResponse($ticket),
            'ticket_use_url' => URL::temporarySignedRoute('user-tickets.use', now()->addMinutes(10), ['user_ticket' => $userTicket->id]),
        ]);
    }

    /**
     * Show an issued ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function showIssuedTicket(Request $request, Ticket $ticket): Response
    {
        TicketService::checkIfUserIsOrganizerForTicket($request->user(), $ticket);

        return Inertia::render('EditIssuedTicket', [
            'ticket' => TicketService::getIssuedTicketResponse($ticket),
        ]);
    }

    /**
     * Store a ticket
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'event_title' => ['required', 'string', 'max:' . TicketConst::EVENT_TITLE_LENGTH_MAX],
            'event_description' => ['nullable', 'string', 'max:' . TicketConst::EVENT_DESCRIPTION_LENGTH_MAX],
            'price' => [
                'required',
                'decimal:0,2',
                'min:' . MoneyService::convertCentsToDollars(TicketConst::PRICE_MIN),
                'max:' . MoneyService::convertCentsToDollars(TicketConst::PRICE_MAX),
            ],
            'number_of_tickets' => ['required', 'integer', 'min:' . TicketConst::NUMBER_OF_TICKETS_MIN, 'max:' . TicketConst::NUMBER_OF_TICKETS_MAX],
            'event_start_date' => ['required', 'date'],
            'event_end_date' => ['required', 'date'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ]);

        $ticketRepository = new TicketRepository();

        $user = $request->user();
        OrganizerService::checkIfUserIsOrganizer($user);

        $startDate = $request->date('start_date');
        $endDate = $request->date('end_date');
        $eventStartDate = $request->date('event_start_date');
        $eventEndDate = $request->date('event_end_date');

        $errorMessage = [];
        if (!TicketService::areEventAndTicketSalesDatesValid($eventStartDate, $eventEndDate, $startDate, $endDate, null, $errorMessage)) {
            return back()->withErrors($errorMessage);
        }

        $price = MoneyService::convertDollarsToCents($request->price);
        [, $stripePrice] = StripeService::createProduct($request->event_title, $request->event_description, $price);

        $ticket = new Ticket([
            'organizer_user_id' => $user->id,
            'event_title' => $request->event_title,
            'event_description' => $request->event_description,
            'price' => $price,
            'stripe_price_id' => $stripePrice->id,
            'number_of_tickets' => $request->number_of_tickets,
            'number_of_reserved_tickets' => 0,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // If additional DB operations are added, wrap only the DB operations in a transaction.
        // Normally, for clarity and to avoid forgetting it later, I wrap the entire method in a transaction even when there is only a single DB operation.
        // However, in this case, I intentionally do not wrap the whole method because external API calls should not be included in a transaction,
        // and a transaction is not necessary for a single DB operation here.
        $ticketRepository->save($ticket);
            
        return redirect()->intended('/issued-tickets');
    }

    /**
     * Update a ticket
     *
     * @param Request $request
     * @param Ticket $ticket
     * @return RedirectResponse
     */
    public function update(Request $request, Ticket $ticket): RedirectResponse
    {
        return DB::transaction(function () use ($request, $ticket) {
            $request->validate([
                'event_title' => ['required', 'string', 'max:' . TicketConst::EVENT_TITLE_LENGTH_MAX],
                'event_description' => ['nullable', 'string', 'max:' . TicketConst::EVENT_DESCRIPTION_LENGTH_MAX],
                'number_of_tickets' => ['required', 'integer', 'min:' . TicketConst::NUMBER_OF_TICKETS_MIN, 'max:' . TicketConst::NUMBER_OF_TICKETS_MAX],
                'event_start_date' => ['required', 'date'],
                'event_end_date' => ['required', 'date'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date'],
            ]);

            $ticketRepository = new TicketRepository();

            TicketService::checkIfUserIsOrganizerForTicket($request->user(), $ticket);

            $startDate = $request->date('start_date');
            $endDate = $request->date('end_date');
            $eventStartDate = $request->date('event_start_date');
            $eventEndDate = $request->date('event_end_date');

            $errorMessage = [];
            if (!TicketService::areEventAndTicketSalesDatesValid($eventStartDate, $eventEndDate, $startDate, $endDate, $ticket, $errorMessage)) {
                return back()->withErrors($errorMessage);
            }

            if (!TicketService::isDuringPeriod($ticket)) {
                $ticket->start_date = $startDate;
            }

            $ticket->end_date = $endDate;
            $ticket->event_start_date = $eventStartDate;
            $ticket->event_end_date = $eventEndDate;

            $ticketRepository->save($ticket);
            
            return back();
        });
    }

    /**
     * Use a ticket
     *
     * @param Request $request
     * @param UserTicket $userTicket
     * @return Response
     */
    public function useTicket(Request $request, UserTicket $userTicket): Response
    {
        return DB::transaction(function () use ($request, $userTicket) {
            $userTicketRepository = new UserTicketRepository();
            $ticketRepository = new TicketRepository();

            TicketService::checkIfTicketIsUsed($userTicket);

            $ticket = $ticketRepository->selectById($userTicket->ticket_id);

            TicketService::checkIfUserIsOrganizerForTicket($request->user(), $ticket);
            TicketService::checkIfTicketIsDuringEvent($ticket);

            $userTicket->used_at = new Carbon();

            $userTicketRepository->save($userTicket);

            return Inertia::render('UseTicket', [
                'userTicketId' => $userTicket->id,
            ]);
        });
    }
}
