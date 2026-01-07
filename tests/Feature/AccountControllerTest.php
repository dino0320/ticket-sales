<?php

namespace Tests\Feature;

use App\Consts\AccountConst;
use App\Consts\CheckoutConst;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\UserOrganizerApplication;
use App\Models\UserTicket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test normal register()
     */
    public function test_register_normal(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Test1234!',
            'password_confirmation' => 'Test1234!',
        ]);

        $response->assertRedirect('/home');

        $user = User::where('email', 'test@example.com')->first();
        $this->assertAuthenticatedAs($user, 'web');
        $this->assertSame('Test', $user->name);
    }

    /**
     * Test abnormal (email duplication) register()
     */
    public function test_register_abnormal_email_duplication(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Test1234!',
            'password_confirmation' => 'Test1234!',
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test abnormal (different password confirmation) register()
     */
    public function test_register_abnormal_different_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'Test1234!',
            'password_confirmation' => 'Test1234',
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test normal authenticate()
     */
    public function test_authenticate_normal(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Test1234!'),
        ]);

        $response = $this->post('/authenticate', [
            'email' => 'test@example.com',
            'password' => 'Test1234!',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user, 'web');
    }

    /**
     * Test abnormal (wrong credentials) authenticate()
     */
    public function test_authenticate_abnormal_wrong_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('Test1234!'),
        ]);

        $response = $this->post('/authenticate', [
            'email' => 'test@example.com',
            'password' => 'Test1234',
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['root']);
        $this->assertGuest('web');
    }

    /**
     * Test signOut()
     */
    public function test_sign_out(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'web')->post('/sign-out');

        $response->assertRedirect('/home');
        $this->assertGuest('web');
    }

    /**
     * Test show()
     */
    public function test_show(): void
    {
        $user = User::factory()->create([
            'id' => 1,
            'is_organizer' => false,
        ]);

        $eventEndDate = (new Carbon())->addYear();
        Ticket::factory()->create([
            'id' => 1,
            'event_title' => 'Event Title 1',
            'event_end_date' => $eventEndDate,
        ]);
        Ticket::factory()->create([
            'id' => 2,
            'event_title' => 'Event Title 2',
            'event_end_date' => (new Carbon())->addYears(-1),
        ]);
        Ticket::factory()->create([
            'id' => 3,
            'event_title' => 'Event Title 3',
            'event_end_date' => $eventEndDate,
        ]);
        UserTicket::factory()->create([
            'id' => 1,
            'user_id' => 1,
            'ticket_id' => 1,
        ]);
        UserTicket::factory()->create([
            'id' => 2,
            'user_id' => 1,
            'ticket_id' => 1,
        ]);;
        UserTicket::factory()->create([
            'id' => 3,
            'user_id' => 1,
            'ticket_id' => 2,
        ]);
        UserTicket::factory()->create([
            'id' => 4,
            'user_id' => 1,
            'ticket_id' => 3,
        ]);

        $response = $this->actingAs($user, 'web')->get('/my-account');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Account')
            ->has('tickets', fn (Assert $page) => $page
                ->has('data', 3)
                ->has('data', fn (Assert $page) => $page
                    ->has(0, fn (Assert $page) => $page
                        ->where('id', 1)
                        ->where('event_title', 'Event Title 1')
                        ->etc()
                    )
                    ->has(1, fn (Assert $page) => $page
                        ->where('id', 2)
                        ->where('event_title', 'Event Title 1')
                        ->etc()
                    )
                    ->has(2, fn (Assert $page) => $page
                        ->where('id', 4)
                        ->where('event_title', 'Event Title 3')
                        ->etc()
                    )
                )
                ->whereType('links', 'array')
                ->whereType('meta', 'array')
            )
            ->where('isOrganizerApplicationApplied', false)
        );
    }

    /**
     * Test showOrderHistory()
     */
    public function test_show_order_history(): void
    {
        $user = User::factory()->create(['id' => 1]);
        
        UserOrder::create([
            'user_id' => 1,
            'amount' => 500,
            'order_items' => [
                [
                    'event_title' => 'Event Title 1',
                    'event_description' => 'Event Description 1',
                    'price' => 100,
                    'number_of_tickets' => 1,
                ],
                [
                    'event_title' => 'Event Title 2',
                    'event_description' => 'Event Description 2',
                    'price' => 200,
                    'number_of_tickets' => 2,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_COMPLETED,
        ]);
        UserOrder::create([
            'user_id' => 1,
            'amount' => 300,
            'order_items' => [
                [
                    'event_title' => 'Event Title 3',
                    'event_description' => 'Event Description 3',
                    'price' => 300,
                    'number_of_tickets' => 1,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_COMPLETED,
        ]);
        UserOrder::create([
            'user_id' => 1,
            'amount' => 400,
            'order_items' => [
                [
                    'event_title' => 'Event Title 4',
                    'event_description' => 'Event Description 4',
                    'price' => 400,
                    'number_of_tickets' => 1,
                ],
            ],
            'status' => CheckoutConst::ORDER_STATUS_PENDING, // Pending orders are not retrieved
        ]);

        $response = $this->actingAs($user, 'web')->get('/order-history');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('OrderHistory')
            ->has('userOrders', fn (Assert $page) => $page
                ->has('data', 2)
                ->has('data', fn (Assert $page) => $page
                    ->has(0, fn (Assert $page) => $page
                        ->whereType('id', 'integer')
                        ->where('amount', 3)
                        ->has('order_items', 1)
                        ->has('order_items', fn (Assert $page) => $page
                            ->has(0, fn (Assert $page) => $page
                                ->where('event_title', 'Event Title 3') // Sort by order date descending
                                ->etc()
                            )
                        )
                        ->whereType('order_date', 'string')
                    )
                    ->has(1, fn (Assert $page) => $page
                        ->whereType('id', 'integer')
                        ->where('amount', 5)
                        ->has('order_items', 2)
                        ->has('order_items', fn (Assert $page) => $page
                            ->has(0, fn (Assert $page) => $page
                                ->where('event_title', 'Event Title 1')
                                ->etc()
                            )
                            ->has(1, fn (Assert $page) => $page
                                ->where('event_title', 'Event Title 2')
                                ->etc()
                            )
                        )
                        ->whereType('order_date', 'string')
                    )
                )
                ->whereType('links', 'array')
                ->whereType('meta', 'array')
            )
        );
    }

    /**
     * Test showIssuedTickets()
     */
    public function test_show_issued_tickets(): void
    {
        $user = User::factory()->create(['id' => 1]);
        User::factory()->create(['id' => 2]);

        Ticket::factory()->create([
            'id' => 1,
            'organizer_user_id' => 1,
        ]);
        Ticket::factory()->create([
            'id' => 2,
            'organizer_user_id' => 2, // Tickets created by other users are not retrieved
        ]);
        Ticket::factory()->create([
            'id' => 3,
            'organizer_user_id' => 1,
        ]);

        $response = $this->actingAs($user, 'web')->get('/issued-tickets');

        $response->assertInertia(fn (Assert $page) => $page
            ->component('IssuedTicketIndex')
            ->has('tickets', fn (Assert $page) => $page
                ->has('data', 2)
                ->has('data', fn (Assert $page) => $page
                    ->has(0, fn (Assert $page) => $page
                        ->where('id', 3) // Sort by ID descending
                        ->etc()
                    )
                    ->has(1, fn (Assert $page) => $page
                        ->where('id', 1)
                        ->etc()
                    )
                )
                ->whereType('links', 'array')
                ->whereType('meta', 'array')
            )
        );
    }

    /**
     * Test normal resetPassword()
     */
    public function test_reset_password_normal(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Test1234!')]);

        $response = $this->actingAs($user, 'web')->post('/reset-password', [
            'password' => 'Test1234!',
            'new_password' => 'Test1234?',
            'new_password_confirmation' => 'Test1234?',
        ]);

        $response->assertRedirect('/my-account');
        $user->refresh();
        $this->assertTrue(Hash::check('Test1234?', $user->password));
    }

    /**
     * Test abnormal (wrong credentials) resetPassword()
     */
    public function test_reset_password_abnormal_wrong_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('Test1234!')]);

        $response = $this->actingAs($user, 'web')->post('/reset-password', [
            'password' => 'Test1234',
            'new_password' => 'Test1234?',
            'new_password_confirmation' => 'Test1234?',
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * Test normal (create application) applyToBeOrganizer()
     */
    public function test_apply_to_be_organizer_normal_create_application(): void
    {
        $user = User::factory()->create(['id' => 1]);

        $response = $this->actingAs($user, 'web')->post('/organizer-application', [
            'event_description' => 'Event Description',
            'is_individual' => true,
            'website_url' => 'https://example.com',
        ]);

        $response->assertRedirect('/my-account');
        $userOrganizerApplication = UserOrganizerApplication::where('user_id', 1)->first();
        $this->assertSame(AccountConst::ORGANIZER_STATUS_PENDING, $userOrganizerApplication->status);
        $this->assertSame('Event Description', $userOrganizerApplication->event_description);
    }

    /**
     * Test normal (update application) applyToBeOrganizer()
     */
    public function test_apply_to_be_organizer_normal_update_application(): void
    {
        $user = User::factory()->create(['id' => 1]);

        UserOrganizerApplication::create([
            'user_id' => 1,
            'status' => AccountConst::ORGANIZER_STATUS_UNAPPROVED,
            'event_description' => 'Event Description 1',
            'is_individual' => true,
            'website_url' => 'https://example.com',
            'applied_at' => new Carbon(),
        ]);

        $response = $this->actingAs($user, 'web')->post('/organizer-application', [
            'event_description' => 'Event Description 2',
            'is_individual' => true,
            'website_url' => 'https://example.com',
        ]);

        $response->assertRedirect('/my-account');
        $userOrganizerApplication = UserOrganizerApplication::where('user_id', 1)->first();
        $this->assertSame(AccountConst::ORGANIZER_STATUS_PENDING, $userOrganizerApplication->status);
        $this->assertSame('Event Description 2', $userOrganizerApplication->event_description);
    }

    /**
     * Test abnormal (already applied) applyToBeOrganizer()
     */
    public function test_apply_to_be_organizer_abnormal_already_applied(): void
    {
        $user = User::factory()->create(['id' => 1]);

        UserOrganizerApplication::create([
            'user_id' => 1,
            'status' => AccountConst::ORGANIZER_STATUS_PENDING,
            'event_description' => 'Event Description 1',
            'is_individual' => true,
            'website_url' => 'https://example.com',
            'applied_at' => new Carbon(),
        ]);

        $response = $this->actingAs($user, 'web')->post('/organizer-application', [
            'event_description' => 'Event Description 2',
            'is_individual' => true,
            'website_url' => 'https://example.com',
        ]);

        $response->assertRedirectBack();
        $response->assertSessionHasErrors(['root']);
    }
}
