<?php

namespace Database\Factories;

use App\Models\Registration;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition()
    {
        return [
            'training_id' => Training::factory(),
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement([
                Registration::STATUS_PENDING,
                Registration::STATUS_APPROVED,
                Registration::STATUS_CANCELLED,
                Registration::STATUS_REJECTED,
                Registration::STATUS_ATTENDED,
                Registration::STATUS_NO_SHOW,
            ]),
        ];
    }
}
