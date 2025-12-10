<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_avatar_is_generated_on_registration(): void
    {
        $response = $this->post('/register', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        
        $user = User::where('email', 'john@example.com')->first();
        
        $this->assertNotNull($user);
        $this->assertNotNull($user->avatar_url);
        $this->assertStringContainsString('ui-avatars.com', $user->avatar_url);
        $this->assertStringContainsString('JD', $user->avatar_url); // Initials
    }

    public function test_avatar_service_generates_correct_initials_for_two_names(): void
    {
        $avatarUrl = AvatarService::generateInitialsAvatar('John Doe');
        
        $this->assertStringContainsString('JD', $avatarUrl);
        $this->assertStringContainsString('ui-avatars.com/api/', $avatarUrl);
        $this->assertStringContainsString('size=200', $avatarUrl);
        $this->assertStringContainsString('bold=true', $avatarUrl);
    }

    public function test_avatar_service_generates_correct_initials_for_single_name(): void
    {
        $avatarUrl = AvatarService::generateInitialsAvatar('John');
        
        $this->assertStringContainsString('JO', $avatarUrl);
    }

    public function test_avatar_service_generates_correct_initials_for_multiple_names(): void
    {
        $avatarUrl = AvatarService::generateInitialsAvatar('John Michael Doe');
        
        $this->assertStringContainsString('JD', $avatarUrl); // First and last name
    }

    public function test_avatar_service_generates_correct_initials_for_vietnamese_names(): void
    {
        $avatarUrl = AvatarService::generateInitialsAvatar('Nguyễn Văn An');
        
        $this->assertStringContainsString('NA', $avatarUrl);
    }

    public function test_user_can_see_avatar_in_header_after_registration(): void
    {
        $user = User::factory()->create([
            'full_name' => 'Jane Smith',
            'avatar_url' => AvatarService::generateInitialsAvatar('Jane Smith'),
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertInertia(fn ($page) => 
            $page->has('auth.user')
                ->where('auth.user.full_name', 'Jane Smith')
                ->where('auth.user.avatar_url', fn ($url) => 
                    str_contains($url, 'ui-avatars.com') && str_contains($url, 'JS')
                )
        );
    }
}
