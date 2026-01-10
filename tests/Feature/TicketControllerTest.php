<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use App\Models\UserTicket;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Inertia\Testing\AssertableInertia as Assert;
use InvalidArgumentException;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test normal show()
     */
    public function test_show_normal(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('TicketDetail')
            ->has('ticket', fn (Assert $page) => $page
                ->where('id', 1)
                ->where('event_title', 'Event Title')
                ->where('event_description', 'Event Description')
                ->where('price', 1) // dollars
                ->where('number_of_tickets', 5)
                ->whereType('event_start_date', 'string')
                ->whereType('event_end_date', 'string')
            )
        );
    }

    /**
     * Test abnormal (out of sales period) show()
     */
    public function test_show_abnormal_out_of_sales_period(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-2),
            'end_date' => (new Carbon())->addYears(-1),
        ]);

        $response = $this->get("/tickets/{$ticket->id}");

        $response->assertStatus(404);
    }

    /**
     * Test normal showUserTicket()
     */
    public function test_show_user_ticket_normal(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => null,
        ]);

        $response = $this->actingAs($user, 'web')->get("/user-tickets/{$userTicket->id}");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('UserTicketDetail')
            ->has('ticket', fn (Assert $page) => $page
                ->where('id', $ticket->id)
                ->where('event_title', 'Event Title')
                ->where('event_description', 'Event Description')
                ->where('price', 1) // dollars
                ->where('number_of_tickets', 5)
                ->whereType('event_start_date', 'string')
                ->whereType('event_end_date', 'string')
            )
            ->whereType('ticket_use_url', 'string')
        );
    }

    /**
     * Test abnormal (event has been ended) showUserTicket()
     */
    public function test_show_user_ticket_abnormal_event_has_been_ended(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(-2),
            'event_end_date' => (new Carbon())->addYears(-1),
            'start_date' => (new Carbon())->addYears(-4),
            'end_date' => (new Carbon())->addYears(-3),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => null,
        ]);

        $response = $this->actingAs($user, 'web')->get("/user-tickets/{$userTicket->id}");

        $response->assertStatus(404);
    }

    /**
     * Test abnormal (ticket has been used) showUserTicket()
     */
    public function test_show_user_ticket_abnormal_ticket_has_been_used(): void
    {
        Exceptions::fake();

        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(-1),
            'event_end_date' => (new Carbon())->addYear(),
            'start_date' => (new Carbon())->addYears(-3),
            'end_date' => (new Carbon())->addYears(-2),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => new Carbon(),
        ]);

        $response = $this->actingAs($user, 'web')->get("/user-tickets/{$userTicket->id}");

        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }

    /**
     * Test normal (during sales period) showIssuedTicket()
     */
    public function test_show_issued_ticket_normal_during_sales_period(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $response = $this->actingAs($user, 'web')->get("/issued-tickets/{$ticket->id}");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('EditIssuedTicket')
            ->has('ticket', fn (Assert $page) => $page
                ->where('id', 1)
                ->where('event_title', 'Event Title')
                ->where('event_description', 'Event Description')
                ->where('price', 1) // dollars
                ->where('initial_number_of_tickets', 5)
                ->where('number_of_tickets', 5)
                ->whereType('event_start_date', 'string')
                ->whereType('event_end_date', 'string')
                ->whereType('start_date', 'string')
                ->whereType('end_date', 'string')
            )
            ->where('isDuringSalesPeriod', true)
        );
    }

    /**
     * Test normal (out of sales period) showIssuedTicket()
     */
    public function test_show_issued_ticket_normal_out_of_sales_period(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-2),
            'end_date' => (new Carbon())->addYears(-1),
        ]);

        $response = $this->actingAs($user, 'web')->get("/issued-tickets/{$ticket->id}");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('EditIssuedTicket')
            ->has('ticket', fn (Assert $page) => $page
                ->where('id', 1)
                ->where('event_title', 'Event Title')
                ->where('event_description', 'Event Description')
                ->where('price', 1) // dollars
                ->where('initial_number_of_tickets', 5)
                ->where('number_of_tickets', 5)
                ->whereType('event_start_date', 'string')
                ->whereType('event_end_date', 'string')
                ->whereType('start_date', 'string')
                ->whereType('end_date', 'string')
            )
            ->where('isDuringSalesPeriod', false)
        );
    }

    /**
     * Test abnormal (user is not organizer) showIssuedTicket()
     */
    public function test_show_issued_ticket_abnormal_user_is_not_organizer(): void
    {
        Exceptions::fake();

        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => false,
        ]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $response = $this->actingAs($user, 'web')->get("/issued-tickets/{$ticket->id}");

        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }

    /**
     * Test abnormal (user is not organizer of ticket) showIssuedTicket()
     */
    public function test_show_issued_ticket_abnormal_user_is_not_organizer_of_ticket(): void
    {
        Exceptions::fake();

        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);
        $organizerUserOfTicket = User::factory()->create([
            'id' => 2,
            'is_organizer' => true,
        ]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUserOfTicket->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2),
            'event_end_date' => (new Carbon())->addYears(3),
            'start_date' => (new Carbon())->addYears(-1),
            'end_date' => (new Carbon())->addYear(),
        ]);

        $response = $this->actingAs($user, 'web')->get("/issued-tickets/{$ticket->id}");

        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }

    /**
     * Test normal store()
     */
    public function test_store_normal(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('createProduct')
            ->once()
            ->andReturn([null, (object) ['id' => 'TEST_PRICE_ID']]);

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->post('/tickets', [
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 1.50,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(3)->toString(),
            'event_end_date' => (new Carbon())->addYears(4)->toString(),
            'start_date' => (new Carbon())->addYear()->toString(),
            'end_date' => (new Carbon())->addYears(2)->toString(),
        ]);

        $response->assertRedirect('/issued-tickets');
        
        $ticket = Ticket::where('organizer_user_id', $user->id)->first();
        $this->assertSame('Event Title', $ticket->event_title);
        $this->assertSame(150, $ticket->price); // cents
    }

    /**
     * Test abnormal (user is not organizer) store()
     */
    public function test_store_abnormal_user_is_not_organizer(): void
    {
        Exceptions::fake();

        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => false,
        ]);

        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('createProduct')->never();

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->post('/tickets', [
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 1.50,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(3)->toString(),
            'event_end_date' => (new Carbon())->addYears(4)->toString(),
            'start_date' => (new Carbon())->addYear()->toString(),
            'end_date' => (new Carbon())->addYears(2)->toString(),
        ]);

        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }

    /**
     * Test abnormal (invalid dates) store()
     */
    public function test_store_abnormal_invalid_dates(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $stripeService = Mockery::mock(StripeService::class);
        $stripeService->shouldReceive('createProduct')->never();

        $this->app->instance(StripeService::class, $stripeService); // bind a StripeService instance into the container

        $response = $this->actingAs($user, 'web')->post('/tickets', [
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 1.50,
            'number_of_tickets' => 5,
            'event_start_date' => (new Carbon())->addYears(2)->toString(),
            'event_end_date' => (new Carbon())->addYears(3)->toString(),
            'start_date' => (new Carbon())->addYears(-1)->toString(),
            'end_date' => (new Carbon())->addYear()->toString(),
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['start_date']);
    }

    /**
     * Test normal (during sales period) update()
     */
    public function test_update_normal_during_sales_period(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $eventStartDate = (new Carbon())->addYears(2);
        $eventEndDate = (new Carbon())->addYears(3);
        $startDate = (new Carbon())->addYears(-1);
        $endDate = (new Carbon())->addYear();
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $updatedEventStartDate = (new Carbon())->addYears(3);
        $updatedEventEndDate = (new Carbon())->addYears(4);
        $updatedEndDate = (new Carbon())->addYears(2);
        $response = $this->actingAs($user, 'web')->put("/tickets/{$ticket->id}", [
            'event_title' => 'Updated Event Title',
            'event_description' => 'Updated Event Description',
            'number_of_tickets' => 5,
            'event_start_date' => $updatedEventStartDate->toString(),
            'event_end_date' => $updatedEventEndDate->toString(),
            'start_date' => $startDate->toString(),
            'end_date' => $updatedEndDate->toString(),
        ]);

        $response->assertRedirectBack();
        
        $ticket->refresh();
        $this->assertSame('Updated Event Title', $ticket->event_title);
        $this->assertSame('Updated Event Description', $ticket->event_description);
        $this->assertSame($updatedEventStartDate->toString(), $ticket->event_start_date->toString());
        $this->assertSame($updatedEventEndDate->toString(), $ticket->event_end_date->toString());
        $this->assertSame($startDate->toString(), $ticket->start_date->toString());
        $this->assertSame($updatedEndDate->toString(), $ticket->end_date->toString());
    }

    /**
     * Test normal (out of sales period) update()
     */
    public function test_update_normal_out_of_sales_period(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $eventStartDate = (new Carbon())->addYears(3);
        $eventEndDate = (new Carbon())->addYears(4);
        $startDate = (new Carbon())->addYear();
        $endDate = (new Carbon())->addYears(2);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $updatedEventStartDate = (new Carbon())->addYears(4);
        $updatedEventEndDate = (new Carbon())->addYears(5);
        $updatedStartDate = (new Carbon())->addYears(2);
        $updatedEndDate = (new Carbon())->addYears(3);
        $response = $this->actingAs($user, 'web')->put("/tickets/{$ticket->id}", [
            'event_title' => 'Updated Event Title',
            'event_description' => 'Updated Event Description',
            'number_of_tickets' => 10,
            'event_start_date' => $updatedEventStartDate->toString(),
            'event_end_date' => $updatedEventEndDate->toString(),
            'start_date' => $updatedStartDate->toString(),
            'end_date' => $updatedEndDate->toString(),
        ]);

        $response->assertRedirectBack();
        
        $ticket->refresh();
        $this->assertSame('Updated Event Title', $ticket->event_title);
        $this->assertSame('Updated Event Description', $ticket->event_description);
        $this->assertSame(10, $ticket->initial_number_of_tickets);
        $this->assertSame(10, $ticket->number_of_tickets);
        $this->assertSame($updatedEventStartDate->toString(), $ticket->event_start_date->toString());
        $this->assertSame($updatedEventEndDate->toString(), $ticket->event_end_date->toString());
        $this->assertSame($updatedStartDate->toString(), $ticket->start_date->toString());
        $this->assertSame($updatedEndDate->toString(), $ticket->end_date->toString());
    }

    /**
     * Test abnormal (user is not organizer) update()
     */
    public function test_update_abnormal_user_is_not_organizer(): void
    {
        Exceptions::fake();

        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => false,
        ]);

        $eventStartDate = (new Carbon())->addYears(3);
        $eventEndDate = (new Carbon())->addYears(4);
        $startDate = (new Carbon())->addYear();
        $endDate = (new Carbon())->addYears(2);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $response = $this->actingAs($user, 'web')->put("/tickets/{$ticket->id}", [
            'event_title' => 'Updated Event Title',
            'event_description' => 'Updated Event Description',
            'number_of_tickets' => 10,
            'event_start_date' => $eventStartDate->toString(),
            'event_end_date' => $eventEndDate->toString(),
            'start_date' => $startDate->toString(),
            'end_date' => $endDate->toString(),
        ]);

        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }

    /**
     * Test abnormal (invalid dates) update()
     */
    public function test_update_abnormal_invalid_dates(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $eventStartDate = (new Carbon())->addYears(3);
        $eventEndDate = (new Carbon())->addYears(4);
        $startDate = (new Carbon())->addYears(-1);
        $endDate = (new Carbon())->addYears(2);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $response = $this->actingAs($user, 'web')->put("/tickets/{$ticket->id}", [
            'event_title' => 'Updated Event Title',
            'event_description' => 'Updated Event Description',
            'number_of_tickets' => 10,
            'event_start_date' => $eventStartDate->toString(),
            'event_end_date' => $eventEndDate->toString(),
            'start_date' => (new Carbon())->addYear()->toString(),
            'end_date' => $endDate->toString(),
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['start_date']);
    }

    /**
     * Test abnormal (invalid stock) update()
     */
    public function test_update_abnormal_invalid_stock(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => true,
        ]);

        $eventStartDate = (new Carbon())->addYears(2);
        $eventEndDate = (new Carbon())->addYears(3);
        $startDate = (new Carbon())->addYear();
        $endDate = (new Carbon())->addYears(1);
        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $user->id,
            'event_title' => 'Event Title',
            'event_description' => 'Event Description',
            'price' => 100,
            'initial_number_of_tickets' => 5,
            'number_of_tickets' => 5,
            'event_start_date' => $eventStartDate,
            'event_end_date' => $eventEndDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $response = $this->actingAs($user, 'web')->put("/tickets/{$ticket->id}", [
            'event_title' => 'Updated Event Title',
            'event_description' => 'Updated Event Description',
            'number_of_tickets' => 3,
            'event_start_date' => $eventStartDate->toString(),
            'event_end_date' => $eventEndDate->toString(),
            'start_date' => $startDate->toString(),
            'end_date' => $endDate->toString(),
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['number_of_tickets']);
    }

    /**
     * Test normal useTicket()
     */
    public function test_use_ticket_normal(): void
    {
        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_start_date' => (new Carbon())->addYears(-1),
            'event_end_date' => (new Carbon())->addYear(),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => null,
        ]);

        $response = $this->actingAs($organizerUser, 'web')->get("/user-tickets/{$userTicket->id}/use");

        $response->assertInertia(fn (Assert $page) => $page
            ->component('UseTicket')
            ->where('userTicketId', $userTicket->id)
        );
        $userTicket->refresh();
        $this->assertNotNull($userTicket->used_at);
    }

    /**
     * Test abnormal (already used) useTicket()
     */
    public function test_use_ticket_abnormal_already_used(): void
    {
        Exceptions::fake();

        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_start_date' => (new Carbon())->addYears(-1),
            'event_end_date' => (new Carbon())->addYear(),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => new Carbon(),
        ]);

        $response = $this->actingAs($organizerUser, 'web')->get("/user-tickets/{$userTicket->id}/use");

        $response->assertStatus(500);
        Exceptions::assertReported(RuntimeException::class);
    }

    /**
     * Test abnormal (user is not organizer) useTicket()
     */
    public function test_use_ticket_abnormal_user_is_not_organizer(): void
    {
        Exceptions::fake();

        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_start_date' => (new Carbon())->addYears(-1),
            'event_end_date' => (new Carbon())->addYear(),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => null,
        ]);

        $response = $this->actingAs($user, 'web')->get("/user-tickets/{$userTicket->id}/use");

        $response->assertStatus(500);
        Exceptions::assertReported(InvalidArgumentException::class);
    }

    /**
     * Test abnormal (out of event period) useTicket()
     */
    public function test_use_ticket_abnormal_out_of_event_period(): void
    {
        Exceptions::fake();

        $user = User::factory()->create(['id' => 1]);
        $organizerUser = User::factory()->create(['id' => 2]);

        $ticket = Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => $organizerUser->id,
            'event_start_date' => (new Carbon())->addYears(-2),
            'event_end_date' => (new Carbon())->addYears(-1),
        ]);

        $userTicket = UserTicket::create([
            'user_id' => $user->id,
            'ticket_id' => $ticket->id,
            'used_at' => null,
        ]);

        $response = $this->actingAs($organizerUser, 'web')->get("/user-tickets/{$userTicket->id}/use");

        $response->assertStatus(500);
        Exceptions::assertReported(InvalidArgumentException::class);
    }
}
