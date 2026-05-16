<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\Sport;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_register_for_upcoming_training(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $sport = Sport::factory()->create();
        $training = Training::factory()->create([
            'sport_id' => $sport->id,
            'date' => now()->addDay()->toDateString(),
            'time' => '18:00:00',
            'is_cancelled' => false,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($user)->post(route('trainings.register', $training));

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'training_id' => $training->id,
            'status' => Registration::STATUS_PENDING,
        ]);
    }

    public function test_user_cannot_register_for_past_training(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $sport = Sport::factory()->create();
        $training = Training::factory()->create([
            'sport_id' => $sport->id,
            'date' => now()->subDay()->toDateString(),
            'time' => '09:00:00',
            'is_cancelled' => false,
            'is_completed' => false,
        ]);

        $response = $this->actingAs($user)->post(route('trainings.register', $training));

        $response->assertRedirect();
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $user->id,
            'training_id' => $training->id,
        ]);
    }

    public function test_cancelled_registration_can_be_reactivated(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $sport = Sport::factory()->create();
        $training = Training::factory()->create([
            'sport_id' => $sport->id,
            'date' => now()->addDays(2)->toDateString(),
            'time' => '11:00:00',
            'is_cancelled' => false,
            'is_completed' => false,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'training_id' => $training->id,
            'status' => Registration::STATUS_CANCELLED,
        ]);

        $response = $this->actingAs($user)->post(route('trainings.reregister', $training));

        $response->assertRedirect();
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => Registration::STATUS_PENDING,
        ]);
    }

    public function test_user_can_cancel_own_registration(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $sport = Sport::factory()->create();
        $training = Training::factory()->create([
            'sport_id' => $sport->id,
            'date' => now()->addDays(3)->toDateString(),
            'time' => '14:30:00',
            'is_cancelled' => false,
            'is_completed' => false,
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'training_id' => $training->id,
            'status' => Registration::STATUS_PENDING,
        ]);

        $response = $this->actingAs($user)->delete(route('registrations.cancel', $registration));

        $response->assertRedirect(route('profile'));
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => Registration::STATUS_CANCELLED,
        ]);
    }
}
