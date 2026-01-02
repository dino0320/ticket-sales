<?php

namespace App\Http\Resources;

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
}
