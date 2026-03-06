<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Coach;

class SportFactory extends Factory
{
    public function definition(): array
    {
        $sports = [
            'Football',
            'Basketball',
            'Volleyball',
            'Tennis',
            'Swimming',
            'Boxing',
            'Wrestling',
            'Karate',
            'Taekwondo',
            'Judo',
            'Gymnastics',
            'Athletics',
            'Cycling',
            'Rowing',
            'Skating',
            'Skiing',
            'Snowboarding',
            'Hockey',
            'Rugby',
            'Handball',
            'Badminton',
            'Table Tennis',
            'Climbing',
            'CrossFit',
            'Yoga',
            'Pilates',
            'Stretching',
            'HIIT',
            'Running',
            'Martial Arts',
        ];

        return [
            'name' => fake()->randomElement($sports),
            'description' => fake()->sentence(8),
            'location' => fake()->city(),
            'coach_id' => Coach::factory(),
        ];
    }
}
