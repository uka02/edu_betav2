<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_learners_and_educators_can_view_the_profile_page(): void
    {
        $users = [
            User::factory()->learner()->create(),
            User::factory()->educator()->create(),
        ];

        foreach ($users as $user) {
            $this->actingAs($user)
                ->get(route('profile.edit'))
                ->assertOk()
                ->assertSee(__('dashboard.manage_profile'))
                ->assertSee('name="first_name"', false)
                ->assertSee('name="email"', false)
                ->assertSee('name="username"', false);
        }
    }

    public function test_learners_and_educators_can_update_their_personal_information(): void
    {
        $users = [
            User::factory()->learner()->create([
                'name' => 'Learner Person',
                'email' => 'learner@example.com',
                'username' => null,
            ]),
            User::factory()->educator()->create([
                'name' => 'Educator Person',
                'email' => 'educator@example.com',
                'username' => 'educator-old',
            ]),
        ];

        foreach ($users as $index => $user) {
            $response = $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
                'first_name' => 'Updated' . $index,
                'last_name' => 'Member',
                'email' => 'updated' . $index . '@example.com',
                'username' => 'updated-user-' . $index,
            ]);

            $response
                ->assertRedirect(route('profile.edit'))
                ->assertSessionHas('success', __('dashboard.profile_updated'));

            $this->assertDatabaseHas('users', [
                'id' => $user->id,
                'name' => 'Updated' . $index . ' Member',
                'email' => 'updated' . $index . '@example.com',
                'username' => 'updated-user-' . $index,
                'role' => $user->role,
            ]);
        }
    }

    public function test_profile_update_allows_clearing_the_username(): void
    {
        $user = User::factory()->learner()->create([
            'name' => 'Single Name',
            'email' => 'single@example.com',
            'username' => 'single-name',
        ]);

        $this->actingAs($user)->put(route('profile.update'), [
            'first_name' => 'Single',
            'last_name' => '',
            'email' => 'single@example.com',
            'username' => '',
        ])->assertRedirect(route('profile.edit'));

        $this->assertNull($user->fresh()->username);
        $this->assertSame('Single', $user->fresh()->name);
    }

    public function test_profile_update_requires_unique_email_and_username(): void
    {
        $user = User::factory()->learner()->create([
            'email' => 'owner@example.com',
            'username' => 'owner-user',
        ]);

        User::factory()->educator()->create([
            'email' => 'taken@example.com',
            'username' => 'taken-user',
        ]);

        $this->actingAs($user)->from(route('profile.edit'))->put(route('profile.update'), [
            'first_name' => 'Owner',
            'last_name' => 'User',
            'email' => 'taken@example.com',
            'username' => 'taken-user',
        ])->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors(['email', 'username']);
    }
}
