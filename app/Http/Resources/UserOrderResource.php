<?php

namespace App\Http\Resources;

use App\Services\MoneyService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => MoneyService::convertCentsToDollars($this->amount),
            'order_items' => OrderItemResource::collection($this->order_items),
            'order_date' => $this->created_at,
        ];
    }
}
