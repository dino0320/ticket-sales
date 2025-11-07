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
        return [
            'event_title' => fake()->name(),
            'event_description' => fake()->text(),
            'price' => fake()->randomNumber(),
            'number_of_tickets' => fake()->randomNumber(),
            'event_start_date' => fake()->date(),
            'event_end_date' => fake()->date(),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
        ];
    }
}
