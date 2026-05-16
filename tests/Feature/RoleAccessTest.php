<?php

namespace Tests\Feature;

use App\Models\Coach;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_user_cannot_open_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_admin_user_can_open_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_non_coach_user_cannot_open_coach_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/coach');

        $response->assertForbidden();
    }

    public function test_coach_user_can_open_coach_dashboard(): void
    {
        $coachUser = User::factory()->create(['role' => 'coach']);
        Coach::factory()->create(['user_id' => $coachUser->id]);

        $response = $this->actingAs($coachUser)->get('/coach');

        $response->assertOk();
    }
}
