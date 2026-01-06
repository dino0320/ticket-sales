<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTicket;
use App\Services\TicketService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    /**
     * Test isDuringSalesPeriod()
     */
    public function test_is_during_sales_period(): void
    {
        $ticket = Ticket::factory()->make([
            'start_date' => '2000/01/01 00:00:00',
            'end_date' => '2000/12/31 23:59:59',
        ]);
        $this->assertFalse(TicketService::isDuringSalesPeriod($ticket, new Carbon('1999/12/31 23:59:59')));
        $this->assertTrue(TicketService::isDuringSalesPeriod($ticket, new Carbon('2000/01/01 00:00:00')));
        $this->assertTrue(TicketService::isDuringSalesPeriod($ticket, new Carbon('2000/01/01 00:00:01')));
        $this->assertTrue(TicketService::isDuringSalesPeriod($ticket, new Carbon('2000/12/31 23:59:59')));
        $this->assertFalse(TicketService::isDuringSalesPeriod($ticket, new Carbon('2001/01/01 00:00:00')));
    }

    /**
     * Test normal checkIfTicketIsDuringEvent()
     */
    public function test_check_if_ticket_is_during_event_normal(): void
    {
        $this->expectNotToPerformAssertions();
        $ticket = Ticket::factory()->make([
            'event_start_date' => '2000/01/01 00:00:00',
            'event_end_date' => '2000/12/31 23:59:59',
        ]);
        TicketService::checkIfTicketIsDuringEvent($ticket, new Carbon('2000/01/01 00:00:00'));
        TicketService::checkIfTicketIsDuringEvent($ticket, new Carbon('2000/01/01 00:00:01'));
        TicketService::checkIfTicketIsDuringEvent($ticket, new Carbon('2000/12/31 23:59:59'));
    }

    /**
     * Test abnormal (before event) checkIfTicketIsDuringEvent()
     */
    public function test_check_if_ticket_is_during_event_abnormal_before_event(): void
    {
        $this->expectException(RuntimeException::class);
        $ticket = Ticket::factory()->make([
            'event_start_date' => '2000/01/01 00:00:00',
            'event_end_date' => '2000/12/31 23:59:59',
        ]);
        TicketService::checkIfTicketIsDuringEvent($ticket, new Carbon('1999/12/31 23:59:59'));
    }

    /**
     * Test abnormal (after event) checkIfTicketIsDuringEvent()
     */
    public function test_check_if_ticket_is_during_event_abnormal_after_event(): void
    {
        $this->expectException(RuntimeException::class);
        $ticket = Ticket::factory()->make([
            'event_start_date' => '2000/01/01 00:00:00',
            'event_end_date' => '2000/12/31 23:59:59',
        ]);
        TicketService::checkIfTicketIsDuringEvent($ticket, new Carbon('2001/01/01 00:00:00'));
    }

    /**
     * Test normal checkIfEventIsNotOver()
     */
    public function test_check_if_event_is_not_over_normal(): void
    {
        $this->expectNotToPerformAssertions();
        $ticket = Ticket::factory()->make([
            'event_end_date' => '2000/12/31 23:59:59',
        ]);
        TicketService::checkIfEventIsNotOver($ticket, new Carbon('2000/12/31 23:59:58'));
        TicketService::checkIfEventIsNotOver($ticket, new Carbon('2000/12/31 23:59:59'));
    }

    /**
     * Test abnormal checkIfEventIsNotOver()
     */
    public function test_check_if_event_is_not_over_abnormal(): void
    {
        $this->expectException(RuntimeException::class);
        $ticket = Ticket::factory()->make([
            'event_end_date' => '2000/12/31 23:59:59',
        ]);
        TicketService::checkIfEventIsNotOver($ticket, new Carbon('2001/01/01 00:00:00'));
    }

    /**
     * Test checkIfNumbersOfTicketsAreValid()
     */
    public function test_check_if_numbers_of_tickets_are_valid(): void
    {
        $this->expectNotToPerformAssertions();
        $tickets = [
            Ticket::factory()->make([
                'id' => 1,
                'number_of_tickets' => 5,
                'number_of_reserved_tickets' => 2,
            ]),
            Ticket::factory()->make([
                'id' => 2,
                'number_of_tickets' => 3,
                'number_of_reserved_tickets' => 0,
            ]),
        ];
        $numbersOfTickets = [
            1 => 3,
            2 => 2,
        ];
        TicketService::checkIfNumbersOfTicketsAreValid($tickets, $numbersOfTickets);
    }

    /**
     * Test normal checkIfNumberOfTicketsIsValid()
     */
    public function test_check_if_number_of_tickets_is_valid_normal(): void
    {
        $this->expectNotToPerformAssertions();
        $ticket1 = Ticket::factory()->make([
            'id' => 1,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 2,
        ]);
        $ticket2 = Ticket::factory()->make([
            'id' => 1,
            'number_of_tickets' => 3,
            'number_of_reserved_tickets' => 0,
        ]);
        TicketService::checkIfNumberOfTicketsIsValid($ticket1, 3);
        TicketService::checkIfNumberOfTicketsIsValid($ticket2, 2);
    }

    /**
     * Test abnormal checkIfNumberOfTicketsIsValid()
     */
    public function test_check_if_number_of_tickets_is_valid_abnormal(): void
    {
        $this->expectException(RuntimeException::class);
        $ticket = Ticket::factory()->make([
            'id' => 1,
            'number_of_tickets' => 5,
            'number_of_reserved_tickets' => 2,
        ]);
        TicketService::checkIfNumberOfTicketsIsValid($ticket, 4);
    }

    /**
     * Test normal checkIfUserIsOrganizerOfTicket()
     */
    public function test_check_if_user_is_organizer_of_ticket_normal(): void
    {
        $this->expectNotToPerformAssertions();
        $user = User::factory()->make([
            'id' => 1,
            'is_organizer' => true,
        ]);
        $ticket = Ticket::factory()->make(['organizer_user_id' => 1]);
        TicketService::checkIfUserIsOrganizerOfTicket($user, $ticket);
    }

    /**
     * Test abnormal (user is not organizer) checkIfUserIsOrganizerOfTicket()
     */
    public function test_check_if_user_is_organizer_of_ticket_abnormal_user_is_not_organizer(): void
    {
        $this->expectException(RuntimeException::class);
        $user = User::factory()->make([
            'id' => 1,
            'is_organizer' => false,
        ]);
        $ticket = Ticket::factory()->make(['organizer_user_id' => 1]);
        TicketService::checkIfUserIsOrganizerOfTicket($user, $ticket);
    }

    /**
     * Test abnormal (different user) checkIfUserIsOrganizerOfTicket()
     */
    public function test_check_if_user_is_organizer_of_ticket_abnormal_different_user(): void
    {
        $this->expectException(RuntimeException::class);
        $user = User::factory()->make([
            'id' => 1,
            'is_organizer' => true,
        ]);
        $ticket = Ticket::factory()->make(['organizer_user_id' => 2]);
        TicketService::checkIfUserIsOrganizerOfTicket($user, $ticket);
    }

    /**
     * Test areEventAndTicketSalesDatesValid() (date orders)
     */
    public function test_are_event_and_ticket_sales_dates_valid_date_orders(): void
    {
        $errorMessage = [];
        $now = new Carbon('2000/01/01 00:00:00');
        // Success
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            null,
            $errorMessage,
            $now
        ));
        // Failure (sales end date is same as sales start date)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/01/01 00:00:00'),
            null,
            $errorMessage,
            $now
        ));
        // Failure (event start date is same as sales end date)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2000/12/31 23:59:59'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            null,
            $errorMessage,
            $now
        ));
        // Failure (event end date is same as event start date)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            null,
            $errorMessage,
            $now
        ));
    }

    /**
     * Test areEventAndTicketSalesDatesValid() (when creating ticket)
     */
    public function test_are_event_and_ticket_sales_dates_valid_when_creating_ticket(): void
    {
        $errorMessage = [];
        // Success (current datetime is before sales start date)
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            null,
            $errorMessage,
            new Carbon('1999/12/31 23:59:59')
        ));
        // Success (current datetime is same as sales start date)
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            null,
            $errorMessage,
            new Carbon('2000/01/01 00:00:00')
        ));
        // Failure (current datetime is after salse start date)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            null,
            $errorMessage,
            new Carbon('2000/01/01 00:00:01')
        ));
    }

    /**
     * Test areEventAndTicketSalesDatesValid() (before sales period)
     */
    public function test_are_event_and_ticket_sales_dates_valid_before_sale(): void
    {
        $ticket = Ticket::factory()->make([
            'start_date' => '2000/01/01 00:00:00',
            'end_date' => '2000/12/31 23:59:59'
        ]);
        $errorMessage = [];
        // Success (current datetime is before sales start date)
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('1999/01/01 00:00:00'),
            new Carbon('1999/12/31 23:59:59'),
            $ticket,
            $errorMessage,
            new Carbon('1998/12/31 23:59:59')
        ));
        // Success (current datetime is same as sales start date)
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('1999/01/01 00:00:00'),
            new Carbon('1999/12/31 23:59:59'),
            $ticket,
            $errorMessage,
            new Carbon('1999/01/01 00:00:00')
        ));
        // Failure (current datetime is after salse start date)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('1999/01/01 00:00:00'),
            new Carbon('1999/12/31 23:59:59'),
            $ticket,
            $errorMessage,
            new Carbon('1999/01/01 00:00:01')
        ));
    }

    /**
     * Test areEventAndTicketSalesDatesValid() (on sale)
     */
    public function test_are_event_and_ticket_sales_dates_valid_on_sale(): void
    {
        $ticket = Ticket::factory()->make([
            'start_date' => '2000/01/01 00:00:00',
            'end_date' => '2000/12/31 23:59:59'
        ]);
        $errorMessage = [];
        // Success
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 00:00:00'),
            $ticket,
            $errorMessage,
            new Carbon('2000/12/31 00:00:00')
        ));
        // Failure (salse start date is different from sales start date of ticket)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:01'),
            new Carbon('2000/12/31 00:00:00'),
            $ticket,
            $errorMessage,
            new Carbon('2000/12/31 00:00:00')
        ));
        // Failure (salse end date is after current datetime)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 00:00:00'),
            $ticket,
            $errorMessage,
            new Carbon('2000/12/31 00:00:01')
        ));
    }

    /**
     * Test areEventAndTicketSalesDatesValid() (after sales period)
     */
    public function test_are_event_and_ticket_sales_dates_valid_after_sale(): void
    {
        $ticket = Ticket::factory()->make([
            'start_date' => '1998/01/01 00:00:00',
            'end_date' => '1998/12/31 23:59:59'
        ]);
        $errorMessage = [];
        // Success (current datetime is before sales start date)
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            $ticket,
            $errorMessage,
            new Carbon('1999/12/31 23:59:59')
        ));
        // Success (current datetime is same as sales start date)
        $this->assertTrue(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            $ticket,
            $errorMessage,
            new Carbon('2000/01/01 00:00:00')
        ));
        // Failure (current datetime is after salse start date)
        $this->assertFalse(TicketService::areEventAndTicketSalesDatesValid(
            new Carbon('2001/01/01 00:00:00'),
            new Carbon('2001/12/31 23:59:59'),
            new Carbon('2000/01/01 00:00:00'),
            new Carbon('2000/12/31 23:59:59'),
            $ticket,
            $errorMessage,
            new Carbon('2000/01/01 00:00:01')
        ));
    }

    /**
     * Test isNumberOfTicketsValid()
     */
    public function test_is_number_of_tickets_valid(): void
    {
        $ticket = Ticket::factory()->make(['number_of_tickets' => 3]);
        $errorMessage = [];
        $this->assertTrue(TicketService::isNumberOfTicketsValid($ticket, 4, $errorMessage));
        $this->assertTrue(TicketService::isNumberOfTicketsValid($ticket, 3, $errorMessage));
        $this->assertFalse(TicketService::isNumberOfTicketsValid($ticket, 2, $errorMessage));
    }

    /**
     * Test normal checkIfTicketIsUsed()
     */
    public function test_check_if_ticket_is_not_used_normal(): void
    {
        $this->expectNotToPerformAssertions();
        $userTicket = new UserTicket(['used_at' => null]);
        TicketService::checkIfTicketIsNotUsed($userTicket);
    }

    /**
     * Test abnormal checkIfTicketIsUsed()
     */
    public function test_check_if_ticket_is_not_used_abnormal(): void
    {
        $this->expectException(RuntimeException::class);
        $userTicket = new UserTicket(['used_at' => '2000/01/01 00:00:00']);
        TicketService::checkIfTicketIsNotUsed($userTicket);
    }

    /**
     * Test updateUserTicketDataInPaginator()
     */
    public function test_update_user_ticket_data_in_paginator(): void
    {
        $userTickets = [
            UserTicket::factory()->make([
                'id' => 1,
                'ticket_id' => 1,
            ]),
            UserTicket::factory()->make([
                'id' => 2,
                'ticket_id' => 1,
            ]),
            UserTicket::factory()->make([
                'id' => 3,
                'ticket_id' => 2,
            ]),
        ];
        $paginator = new LengthAwarePaginator($userTickets, 3, 10);
        $tickets = [
            Ticket::factory()->make([
                'id' => 1,
                'event_title' => 'Test Event 1',
                'event_description' => 'Test Event 1 Description',
                'price' => 100,
                'event_start_date' => '2001/01/01 00:00:00',
                'event_end_date' => '2001/12/31 23:59:59',
            ]),
            Ticket::factory()->make([
                'id' => 2,
                'event_title' => 'Test Event 2',
                'event_description' => 'Test Event 2 Description',
                'price' => 200,
                'event_start_date' => '2002/01/01 00:00:00',
                'event_end_date' => '2002/12/31 23:59:59',
            ]),
        ];
        TicketService::updateUserTicketDataInPaginator($paginator, $tickets);
        $userTicketData = $paginator->getCollection()->all();
        $this->assertCount(3, $userTicketData);
        $this->assertSame('Test Event 1', $userTicketData[0]['event_title']);
        $this->assertSame('Test Event 1', $userTicketData[1]['event_title']);
        $this->assertSame('Test Event 2', $userTicketData[2]['event_title']);
    }
}
