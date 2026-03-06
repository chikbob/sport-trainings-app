<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CoachFactory extends Factory
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
            'user_id' => User::factory()->state([
                'role' => 'coach',
            ]),
            'bio' => fake()->sentence(10),
            'phone' => fake()->phoneNumber(),
            'specialization' => fake()->randomElement($sports),
        ];
    }
}
