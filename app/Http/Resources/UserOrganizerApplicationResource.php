<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrganizerApplicationResource extends JsonResource
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
            'user_id' => $this->user_id,
            'event_description' => $this->event_description,
            'is_individual' => $this->is_individual,
            'website_url' => $this->website_url,
            'applied_at' => $this->applied_at,
        ];
    }
}
