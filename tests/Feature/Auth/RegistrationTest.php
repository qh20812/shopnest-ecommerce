<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    /**
     * Test registration screen can be rendered
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('auth/register')
        );
    }

    /**
     * Test new users can register with valid data
     */
    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
        
        // Verify user was created in database
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'is_active' => true,
        ]);
    }

    /**
     * Test registration fails with missing full name
     */
    public function test_registration_fails_without_full_name(): void
    {
        $response = $this->post(route('register.store'), [
            'full_name' => '',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('full_name');
        $this->assertGuest();
    }

    /**
     * Test registration fails with invalid email
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        $response = $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test registration fails with duplicate email
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        // Create existing user
        User::create([
            'full_name' => 'Existing User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $response = $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test registration fails when password confirmation does not match
     */
    public function test_registration_fails_with_mismatched_password_confirmation(): void
    {
        $response = $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test registration fails with weak password
     */
    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test user password is hashed after registration
     */
    public function test_user_password_is_hashed(): void
    {
        $password = 'Password123!';
        
        $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', 'test@example.com')->first();
        
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Test registered event is fired
     */
    public function test_registered_event_is_fired(): void
    {
        Event::fake();

        $this->post(route('register.store'), [
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        Event::assertDispatched(Registered::class);
    }
}
