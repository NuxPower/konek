<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertStatus(200)
            ->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store, private');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(['role' => 'freelancer']);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('freelancer.dashboard'));
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_each_role_is_sent_directly_to_its_dashboard(): void
    {
        foreach ([
            'admin' => 'admin.dashboard',
            'client' => 'client.dashboard',
            'freelancer' => 'freelancer.dashboard',
        ] as $role => $route) {
            $user = User::factory()->create([
                'role' => $role,
                'email' => "{$role}@cmu.edu.ph",
            ]);

            $response = $this->post('/login', [
                'email' => $user->email,
                'password' => 'password',
            ]);

            $response->assertRedirect(route($route));
            $this->assertAuthenticatedAs($user);

            $this->post('/logout')->assertRedirect('/');
            $this->assertGuest();
        }
    }

    public function test_login_discards_a_stale_intended_url_from_another_role(): void
    {
        $client = User::factory()->create([
            'role' => 'client',
            'email' => 'client@cmu.edu.ph',
        ]);

        $response = $this
            ->withSession(['url.intended' => route('admin.dashboard')])
            ->post('/login', [
                'email' => $client->email,
                'password' => 'password',
            ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertAuthenticatedAs($client);
        $this->assertNull(session('url.intended'));
    }
}
