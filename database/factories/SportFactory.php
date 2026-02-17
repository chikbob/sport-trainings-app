<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coach;

class SportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Футбол', 'Баскетбол', 'Плавання', 'Волейбол', 'Карате']),
            'description' => fake()->sentence(8),
            'location' => fake()->city(),
            'coach_id' => Coach::factory(),
        ];
    }
}
