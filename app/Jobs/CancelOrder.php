<?php

namespace App\Jobs;

use App\Consts\CheckoutConst;
use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Services\CheckoutService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelOrder implements ShouldQueue
{
    use Queueable;

    private int $userOrderId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userOrderId)
    {
        $this->userOrderId = $userOrderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $userOrderRepository = new UserOrderRepository();
            $ticketRepository = new TicketRepository();
        
            $userOrder = $userOrderRepository->selectById($this->userOrderId);
            if ($userOrder === null || $userOrder->status !== CheckoutConst::ORDER_STATUS_PENDING) {
                return;
            }

            $tickets = $ticketRepository->selectByIdsForUpdate(array_column($userOrder->order_items, 'ticket_id'));
            $numbersOfTickets = CheckoutService::getNumbersOfTickets($userOrder);
            CheckoutService::decreaseNumbersOfReservedTickets($tickets, $numbersOfTickets);

            $userOrder->status = CheckoutConst::ORDER_STATUS_CANCELED;
 
            $userOrderRepository->save($userOrder);
            $ticketRepository->upsert($tickets);

            Log::info('The order is canceled.', [
                'user_order_id' => $this->userOrderId,
            ]);
        });
    }

    public function getUserOrderId(): int
    {
        return $this->userOrderId;
    }
}
