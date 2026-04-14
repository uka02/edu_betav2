<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class AuthOnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_signup_choice_page_shows_all_three_public_paths(): void
    {
        $this->get(route('signup'))
            ->assertOk()
            ->assertSee(route('signup.learner'), false)
            ->assertSee(route('signup.educator'), false)
            ->assertSee(route('login'), false);
    }

    public function test_learner_registration_persists_learner_role(): void
    {
        $response = $this->post(route('signup.post'), [
            'first_name' => 'Lena',
            'last_name' => 'Learner',
            'email' => 'learner@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role' => User::ROLE_LEARNER,
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'learner@example.com',
            'role' => User::ROLE_LEARNER,
        ]);
    }

    public function test_educator_registration_persists_educator_role(): void
    {
        $response = $this->post(route('signup.post'), [
            'first_name' => 'Eda',
            'last_name' => 'Educator',
            'email' => 'educator@example.com',
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
            'role' => User::ROLE_EDUCATOR,
            'terms' => 'on',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'educator@example.com',
            'role' => User::ROLE_EDUCATOR,
        ]);
    }

    public function test_google_callback_creates_educator_when_signup_intent_is_educator(): void
    {
        Socialite::shouldReceive('driver->user')->once()->andReturn(
            new class implements SocialiteUser
            {
                public function getId()
                {
                    return 'google-educator-1';
                }

                public function getNickname()
                {
                    return null;
                }

                public function getName()
                {
                    return 'Educator Example';
                }

                public function getEmail()
                {
                    return 'educator-google@example.com';
                }

                public function getAvatar()
                {
                    return 'https://example.com/educator-avatar.png';
                }
            }
        );

        $response = $this->withSession([
            'google_auth_context' => [
                'context' => 'signup',
                'role' => User::ROLE_EDUCATOR,
            ],
        ])->get(route('google.callback'));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'educator-google@example.com',
            'role' => User::ROLE_EDUCATOR,
        ]);
    }
}
