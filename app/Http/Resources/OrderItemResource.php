<?php

namespace App\Http\Resources;

use App\Services\MoneyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_title' => $this['event_title'],
            'event_description' => $this['event_description'],
            'price' => MoneyService::convertCentsToDollars($this['price']),
            'number_of_tickets' => $this['number_of_tickets'],
        ];
    }
}
