<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CoachFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'role' => 'coach',
            ]),
            'bio' => fake()->sentence(10),
            'phone' => fake()->phoneNumber(),
            'specialization' => fake()->randomElement([
                'Футбол',
                'Баскетбол',
                'Плавання',
                'Волейбол',
                'Карате'
            ]),
        ];
    }
}
