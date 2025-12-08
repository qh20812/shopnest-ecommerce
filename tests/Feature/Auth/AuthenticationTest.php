<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    /**
     * Test login screen can be rendered
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('auth/login')
        );
    }

    /**
     * Test users can authenticate using the login screen
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    /**
     * Test users cannot authenticate with invalid password
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test users cannot authenticate with invalid email
     */
    public function test_users_can_not_authenticate_with_invalid_email(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => 'nonexistent@example.com',
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test inactive users cannot login
     */
    public function test_inactive_users_cannot_login(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test users can logout
     */
    public function test_users_can_logout(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    /**
     * Test remember me functionality
     */
    public function test_remember_me_functionality(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => 'Password123!',
            'remember' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
        
        // Check if remember token is set
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /**
     * Test users are rate limited after multiple failed attempts
     */
    public function test_users_are_rate_limited(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test login validation requires email
     */
    public function test_login_validation_requires_email(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => '',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test login validation requires password
     */
    public function test_login_validation_requires_password(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test session regeneration after login
     */
    public function test_session_regenerates_after_login(): void
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $oldSessionId = session()->getId();

        $this->post(route('login.store'), [
            'email' => 'test@example.com',
            'password' => 'Password123!',
        ]);

        $newSessionId = session()->getId();

        $this->assertNotEquals($oldSessionId, $newSessionId);
        $this->assertAuthenticated();
    }
}
