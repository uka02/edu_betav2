<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSwitchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_browser_preferred_language_is_used_when_session_locale_is_missing(): void
    {
        $response = $this
            ->withHeader('Accept-Language', 'en-US,en;q=0.9')
            ->get(route('login'));

        $response
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee(__('auth.sign_in', [], 'en'));
    }

    public function test_posting_locale_update_persists_the_selected_locale(): void
    {
        $response = $this
            ->from(route('login'))
            ->post(route('locale.update'), ['locale' => 'en']);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('locale', 'en');

        $this
            ->withSession(['locale' => 'en'])
            ->get(route('login'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee(__('auth.sign_in', [], 'en'));
    }

    public function test_logout_preserves_the_selected_locale(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession(['locale' => 'en'])
            ->post(route('logout'));

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHas('locale', 'en')
            ->assertSessionHas('success', __('auth.logged_out', [], 'en'));
    }
}
