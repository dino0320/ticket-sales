<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numberOfTickets = fake()->randomNumber();
        return [
            'organizer_user_id' => 1,
            'event_title' => fake()->name(),
            'event_description' => fake()->text(),
            'price' => fake()->randomNumber(),
            'stripe_price_id' => 'price_1SUFodHlSPswWVDq15Vjkymj',
            'initial_number_of_tickets' => $numberOfTickets,
            'number_of_tickets' => $numberOfTickets,
            'number_of_reserved_tickets' => 0,
            'event_start_date' => fake()->date(),
            'event_end_date' => fake()->date(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
        ];
    }
}
