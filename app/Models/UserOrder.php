<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    /** @use HasFactory<\Database\Factories\UserOrderFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'amount',
        'order_items',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'order_items' => 'array',
        ];
    }

    /**
     * Create order item
     *
     * @param Ticket $ticket
     * @param int[] $numberOfTickets
     * @return array
     */
    public static function createOrderItem(Ticket $ticket, int $numberOfTickets): array
    {
        return [
            'ticket_id' => $ticket->id,
            'event_title' => $ticket->event_title,
            'event_description' => $ticket->event_description,
            'price' => $ticket->price,
            'stripe_price_id' => $ticket->stripe_price_id,
            'number_of_tickets' => $numberOfTickets,
        ];
    }
}
