<?php

namespace Tests\Feature;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class GoogleAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_callback_links_existing_user_without_overwriting_password(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'password' => Hash::make('secret-password'),
        ]);
        $originalPassword = $user->password;

        Socialite::shouldReceive('driver->user')->once()->andReturn(
            new class implements SocialiteUser
            {
                public function getId()
                {
                    return 'google-user-1';
                }

                public function getNickname()
                {
                    return null;
                }

                public function getName()
                {
                    return 'Existing User';
                }

                public function getEmail()
                {
                    return 'existing@example.com';
                }

                public function getAvatar()
                {
                    return 'https://example.com/avatar.png';
                }
            }
        );

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertSame($originalPassword, $user->fresh()->password);
        $this->assertSame('google-user-1', $user->fresh()->google_id);
    }

    public function test_google_callback_hides_provider_errors_from_users(): void
    {
        Socialite::shouldReceive('driver->user')
            ->once()
            ->andThrow(new Exception('Sensitive OAuth failure details'));

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', __('auth.google_sign_in_failed'));
    }

    public function test_google_login_does_not_create_account_for_unknown_user(): void
    {
        Socialite::shouldReceive('driver->user')->once()->andReturn(
            new class implements SocialiteUser
            {
                public function getId()
                {
                    return 'google-new-user';
                }

                public function getNickname()
                {
                    return null;
                }

                public function getName()
                {
                    return 'Unknown User';
                }

                public function getEmail()
                {
                    return 'unknown@example.com';
                }

                public function getAvatar()
                {
                    return 'https://example.com/avatar-new.png';
                }
            }
        );

        $response = $this->withSession([
            'google_auth_context' => [
                'context' => 'login',
            ],
        ])->get(route('google.callback'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', __('auth.account_not_found_signup_first'));
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'unknown@example.com',
        ]);
    }
}
