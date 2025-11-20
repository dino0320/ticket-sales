<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'stripe_price_ids',
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
            'stripe_price_ids' => 'array',
        ];
    }
}
