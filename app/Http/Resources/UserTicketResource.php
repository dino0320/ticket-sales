<?php

namespace App\Http\Resources;

use App\Models\Ticket;
use App\Models\UserTicket;
use App\Services\MoneyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserTicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'],
            'event_title' => $this['event_title'],
            'event_description' => $this['event_description'],
            'price' => MoneyService::convertCentsToDollars($this['price']),
            'event_start_date' => $this['event_start_date'],
            'event_end_date' => $this['event_end_date'],
        ];
    }

    /**
     * Create user ticket resource
     *
     * @param UserTicket $userTicket
     * @param Ticket $ticket
     * @return array
     */
    public static function createUserTicketResource(UserTicket $userTicket, Ticket $ticket): array
    {
        return [
            'id' => $userTicket->id,
            'event_title' => $ticket->event_title,
            'event_description' => $ticket->event_description,
            'price' => MoneyService::convertCentsToDollars($ticket->price),
            'event_start_date' => $ticket->event_start_date,
            'event_end_date' => $ticket->event_end_date,
        ];
    }
}
