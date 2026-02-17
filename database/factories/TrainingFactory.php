<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        $now = Carbon::now();

        // Начало прошлого месяца
        $startDate = $now->copy()->subMonth()->startOfMonth();

        // Конец следующего месяца
        $endDate = $now->copy()->addMonth()->endOfMonth();

        return [
            'sport_id' => Sport::factory(),
            'date'     => $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d'),
            'time'     => $this->faker->time('H:i'),
            'place'    => $this->faker->city(),
        ];
    }
}
