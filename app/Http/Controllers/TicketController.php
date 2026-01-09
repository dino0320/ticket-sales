<?php

namespace App\Http\Controllers;

use App\Consts\TicketConst;
use App\Http\Resources\IssuedTicketResource;
use App\Http\Resources\TicketResource;
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
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TicketController extends Controller
{
    /**
     * Show ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function show(Ticket $ticket): Response
    {
        if (!TicketService::isDuringSalesPeriod($ticket)) {
            throw new NotFoundHttpException("The ticket is outside the sales period. ticket_id: {$ticket->id}");
        }

        return Inertia::render('TicketDetail', [
            'ticket' => new TicketResource($ticket),
        ]);
    }

    /**
     * Show purchased ticket detail
     *
     * @param UserTicket $ticket
     * @return Response
     */
    public function showUserTicket(UserTicket $userTicket): Response
    {
        $ticketRepository = new TicketRepository();

        $ticket = $ticketRepository->selectById($userTicket->ticket_id);

        TicketService::checkIfEventIsNotOver($ticket);

        TicketService::checkIfTicketIsNotUsed($userTicket);

        return Inertia::render('UserTicketDetail', [
            'ticket' => new TicketResource($ticket),
            'ticket_use_url' => URL::temporarySignedRoute('user-tickets.use', now()->addMinutes(TicketConst::QR_CODE_EXPIRATION), ['user_ticket' => $userTicket->id]),
        ]);
    }

    /**
     * Show issued ticket detail
     *
     * @param Ticket $ticket
     * @return Response
     */
    public function showIssuedTicket(Request $request, Ticket $ticket): Response
    {
        TicketService::checkIfUserIsOrganizerOfTicket($request->user(), $ticket);

        return Inertia::render('EditIssuedTicket', [
            'ticket' => new IssuedTicketResource($ticket),
            'isDuringSalesPeriod' => TicketService::isDuringSalesPeriod($ticket),
        ]);
    }

    /**
     * Store ticket
     *
     * @param Request $request
     * @param StripeService $stripeService
     * @return RedirectResponse
     */
    public function store(Request $request, StripeService $stripeService): RedirectResponse
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
            throw ValidationException::withMessages($errorMessage);
        }

        $price = MoneyService::convertDollarsToCents($request->price);
        [, $stripePrice] = $stripeService->createProduct($request->event_title, $request->event_description, $price);

        $ticket = new Ticket([
            'organizer_user_id' => $user->id,
            'event_title' => $request->event_title,
            'event_description' => $request->event_description,
            'price' => $price,
            'stripe_price_id' => $stripePrice->id,
            'initial_number_of_tickets' => $request->number_of_tickets,
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
     * Update ticket
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

            TicketService::checkIfUserIsOrganizerOfTicket($request->user(), $ticket);

            $startDate = $request->date('start_date');
            $endDate = $request->date('end_date');
            $eventStartDate = $request->date('event_start_date');
            $eventEndDate = $request->date('event_end_date');

            $errorMessage = [];
            if (!TicketService::isNumberOfTicketsCurrentNumberOrMore($ticket, $request->number_of_tickets, $errorMessage)) {
                throw ValidationException::withMessages($errorMessage);
            }
            
            if (!TicketService::areEventAndTicketSalesDatesValid($eventStartDate, $eventEndDate, $startDate, $endDate, $ticket, $errorMessage)) {
                throw ValidationException::withMessages($errorMessage);
            }
            
            if (!TicketService::isDuringSalesPeriod($ticket)) {
                $ticket->initial_number_of_tickets += ($request->number_of_tickets - $ticket->number_of_tickets);
                $ticket->number_of_tickets = $request->number_of_tickets;
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
     * Use ticket
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

            TicketService::checkIfTicketIsNotUsed($userTicket);

            $ticket = $ticketRepository->selectById($userTicket->ticket_id);

            TicketService::checkIfUserIsOrganizerOfTicket($request->user(), $ticket);
            TicketService::checkIfTicketIsDuringEvent($ticket);

            $userTicket->used_at = new Carbon();

            $userTicketRepository->save($userTicket);

            return Inertia::render('UseTicket', [
                'userTicketId' => $userTicket->id,
            ]);
        });
    }
}
