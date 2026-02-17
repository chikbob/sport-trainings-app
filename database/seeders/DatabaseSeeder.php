<?php

namespace Database\Seeders;

use App\Models\Coach;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sport;
use App\Models\Training;
use App\Models\Registration;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Создаем 3 тренера (user + coach)
        $coachUsers = User::factory(3)->create([
            'role' => 'coach'
        ]);

        $coaches = collect();

        foreach ($coachUsers as $user) {
            $coaches->push(
                Coach::create([
                    'user_id' => $user->id,
                    'bio' => fake()->paragraph(),
                    'specialization' => fake()->randomElement(['Футбол', 'Плавання', 'Карате']),
                ])
            );
        }

        // 2. Создаем секции + назначаем тренера + создаем тренировки
        Sport::factory(5)
            ->create()
            ->each(function ($sport) use ($coaches) {

                $sport->update([
                    'coach_id' => $coaches->random()->id
                ]);

                Training::factory(3)->create([
                    'sport_id' => $sport->id
                ]);
            });

        // 3. Обычные пользователи
        $users = User::factory(15)->create([
            'role' => 'user',
        ]);

        $trainings = Training::all();

        foreach ($users as $user) {
            Registration::factory(rand(1, 3))->create([
                'user_id' => $user->id,
                'training_id' => $trainings->random()->id,
            ]);
        }

        // 4. Админы
        User::create([
            'name' => 'Admin One',
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
