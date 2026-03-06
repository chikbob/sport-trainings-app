<?php

namespace Database\Seeders;

use App\Models\Coach;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sport;
use App\Models\Training;
use App\Models\Registration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Создаем 10 тренеров (user + coach)
        $coachUsers = User::factory(10)->create([
            'role' => 'coach'
        ]);

        $coaches = collect();

        $sportsList = [
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

        foreach ($coachUsers as $user) {
            $coaches->push(
                Coach::create([
                    'user_id' => $user->id,
                    'bio' => fake()->paragraph(),
                    'specialization' => fake()->randomElement($sportsList),
                ])
            );
        }

        // 2. Создаем секции + назначаем тренера + создаем тренировки
        Sport::factory(30)
            ->create()
            ->each(function ($sport) use ($coaches) {

                $sport->update([
                    'coach_id' => $coaches->random()->id
                ]);

                Training::factory(2)->create([
                    'sport_id' => $sport->id
                ]);
            });

        // 3. Обычные пользователи
        $users = User::factory(40)->create([
            'role' => 'user',
        ]);

        $trainings = Training::all();

        foreach ($users as $user) {
            Registration::factory(rand(1, 2))->create([
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

        // 5. Тестовый пользователь для демонстрации
        $demoUser = User::create([
            'name' => 'Demo User',
            'email' => 'test@yandex.ru',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // Тестовый тренер
        $demoCoachUser = User::create([
            'name' => 'Demo Coach',
            'email' => 'coach@yandex.ru',
            'password' => Hash::make('password'),
            'role' => 'coach',
        ]);

        $demoCoach = Coach::create([
            'user_id' => $demoCoachUser->id,
            'bio' => 'Demo coach for testing.',
            'phone' => '+10000000000',
            'specialization' => fake()->randomElement($sportsList),
        ]);

        $demoSport = Sport::factory()->create([
            'coach_id' => $demoCoach->id,
        ]);

        $today = Carbon::today();
        $demoTrainings = collect([
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->subDays(14)->toDateString(),
                'time' => '09:30',
                'place' => 'Main Hall',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->subDays(10)->toDateString(),
                'time' => '18:00',
                'place' => 'Gym A',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->subDays(7)->toDateString(),
                'time' => '11:00',
                'place' => 'Stadium',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->subDays(3)->toDateString(),
                'time' => '17:30',
                'place' => 'Court 1',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->subDays(1)->toDateString(),
                'time' => '12:00',
                'place' => 'Pool',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->toDateString(),
                'time' => '14:00',
                'place' => 'Studio B',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->addDays(2)->toDateString(),
                'time' => '10:00',
                'place' => 'Track',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->addDays(5)->toDateString(),
                'time' => '19:00',
                'place' => 'Court 2',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->addDays(8)->toDateString(),
                'time' => '08:30',
                'place' => 'Gym C',
            ]),
            Training::create([
                'sport_id' => $demoSport->id,
                'date' => $today->copy()->addDays(12)->toDateString(),
                'time' => '20:00',
                'place' => 'Arena',
            ]),
        ]);

        $statuses = [
            Registration::STATUS_APPROVED,
            Registration::STATUS_PENDING,
            Registration::STATUS_CANCELLED,
            Registration::STATUS_REJECTED,
            Registration::STATUS_ATTENDED,
            Registration::STATUS_NO_SHOW,
        ];

        foreach ($demoTrainings as $idx => $training) {
            Registration::create([
                'user_id' => $demoUser->id,
                'training_id' => $training->id,
                'status' => $statuses[$idx % count($statuses)],
            ]);

            Registration::create([
                'user_id' => $users->random()->id,
                'training_id' => $training->id,
                'status' => $statuses[($idx + 1) % count($statuses)],
            ]);

            Registration::create([
                'user_id' => $users->random()->id,
                'training_id' => $training->id,
                'status' => $statuses[($idx + 2) % count($statuses)],
            ]);
        }
    }
}
