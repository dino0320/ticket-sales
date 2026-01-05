<?php

namespace Database\Factories;

use App\Consts\CheckoutConst;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserOrder>
 */
class UserOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'amount' => fake()->randomNumber(),
            'order_items' => [
                [
                    'ticket_id' => 1,
                    'event_title' => fake()->name(),
                    'event_description' => fake()->text(),
                    'price' => fake()->randomNumber(),
                    'stripe_price_id' => fake()->uuid(),
                    'number_of_tickets' => fake()->randomNumber(),
                ],
            ],
            'status' => fake()->randomElement([CheckoutConst::ORDER_STATUS_PENDING, CheckoutConst::ORDER_STATUS_COMPLETED, CheckoutConst::ORDER_STATUS_CANCELED]),
        ];
    }
}
