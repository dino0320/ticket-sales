<?php

namespace Database\Factories;

use App\Consts\TicketConst;
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
        $numberOfTickets = fake()->numberBetween(TicketConst::NUMBER_OF_TICKETS_MIN, TicketConst::NUMBER_OF_TICKETS_MAX);
        return [
            'organizer_user_id' => 1,
            'event_title' => fake()->name(),
            'event_description' => fake()->text(),
            'price' => fake()->numberBetween(TicketConst::PRICE_MIN, TicketConst::PRICE_MAX),
            'stripe_price_id' => 'price_1SUFodHlSPswWVDq15Vjkymj',
            'initial_number_of_tickets' => $numberOfTickets,
            'number_of_tickets' => $numberOfTickets,
            'number_of_reserved_tickets' => 0,
            'event_start_date' => fake()->dateTimeBetween('3 years', '4 years'),
            'event_end_date' => fake()->dateTimeBetween('5 years', '6 years'),
            'start_date' => fake()->dateTimeBetween('-2 years', '-1 year'),
            'end_date' => fake()->dateTimeBetween('1 year', '2 years'),
        ];
    }
}
