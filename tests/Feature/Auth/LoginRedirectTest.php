<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_seller_redirects_to_dashboard_after_login(): void
    {
        // Ensure role exists
        $sellerRole = Role::firstOrCreate(['role_name' => 'seller'], ['description' => 'Seller']);

        // Create seller user
        $seller = User::create([
            'full_name' => 'Login Seller',
            'email' => 'login-seller@test.com',
            'password' => Hash::make('Password123!'),
            'is_active' => true,
        ]);

        $seller->roles()->attach($sellerRole);

        $response = $this->post(route('login.store'), [
            'email' => $seller->email,
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('seller.dashboard'));
    }
}
