<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('user.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $token = Str::random(64);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'session_login_token' => $token,
        ]);

        $this->actingAs($user);

        $this->withSession([
            'single_session_token' => $token,
        ]);

        $response = $this->get(route('user.dashboard'));

        $response->assertOk();
    }
}
