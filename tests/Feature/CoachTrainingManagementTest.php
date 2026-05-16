<?php

namespace Tests\Feature;

use App\Models\Coach;
use App\Models\Registration;
use App\Models\Sport;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachTrainingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_complete_own_training_and_update_registration_statuses(): void
    {
        $coachUser = User::factory()->create(['role' => 'coach']);
        $coach = Coach::factory()->create(['user_id' => $coachUser->id]);
        $sport = Sport::factory()->create(['coach_id' => $coach->id]);
        $training = Training::factory()->create([
            'sport_id' => $sport->id,
            'date' => now()->toDateString(),
            'time' => '19:00:00',
            'is_cancelled' => false,
            'is_completed' => false,
        ]);

        $pendingUser = User::factory()->create(['role' => 'user']);
        $rejectedUser = User::factory()->create(['role' => 'user']);

        $pendingRegistration = Registration::create([
            'user_id' => $pendingUser->id,
            'training_id' => $training->id,
            'status' => Registration::STATUS_PENDING,
        ]);

        $rejectedRegistration = Registration::create([
            'user_id' => $rejectedUser->id,
            'training_id' => $training->id,
            'status' => Registration::STATUS_REJECTED,
        ]);

        $response = $this->actingAs($coachUser)->post(route('coach.trainings.complete', $training));

        $response->assertRedirect();
        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'is_completed' => 1,
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $pendingRegistration->id,
            'status' => Registration::STATUS_ATTENDED,
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $rejectedRegistration->id,
            'status' => Registration::STATUS_NO_SHOW,
        ]);
    }

    public function test_coach_cannot_manage_foreign_training(): void
    {
        $coachUser = User::factory()->create(['role' => 'coach']);
        $coach = Coach::factory()->create(['user_id' => $coachUser->id]);

        $foreignCoach = Coach::factory()->create();
        $sport = Sport::factory()->create(['coach_id' => $foreignCoach->id]);
        $training = Training::factory()->create([
            'sport_id' => $sport->id,
            'date' => now()->addDay()->toDateString(),
            'time' => '10:00:00',
        ]);

        $response = $this->actingAs($coachUser)->post(route('coach.trainings.complete', $training));

        $response->assertForbidden();
        $this->assertDatabaseHas('trainings', [
            'id' => $training->id,
            'is_completed' => 0,
        ]);
    }
}
