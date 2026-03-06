<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Registration;
use App\Models\Sport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class TrainingController extends Controller
{
    /**
     * Список тренировок (календарь)
     */
    public function index()
    {
        $trainings = Training::with('sport')
            ->where(function ($q) {
                $q->whereDate('date', '>', Carbon::today())
                    ->orWhere(function ($q2) {
                        $q2->whereDate('date', Carbon::today())
                            ->where('time', '>=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->orderBy('date')
            ->get();

        return Inertia::render('Trainings/Index', [
            'trainings' => $trainings,
        ]);
    }

    /**
     * Страница одной тренировки
     */
    public function show(Training $training)
    {
        $training->load('sport')
            ->loadCount([
                'registrations as registrations_count' => function ($q) {
                    $q->where('status', '!=', Registration::STATUS_CANCELLED);
                },
            ]);

        $isRegistered = false;
        $canManage = false;
        $coachRegistrations = [];

        if (Auth::check()) {
            $isRegistered = Registration::where('user_id', Auth::id())
                ->where('training_id', $training->id)
                ->where('status', '!=', Registration::STATUS_CANCELLED)
                ->exists();

            if (Auth::user()->role === 'coach') {
                $coach = \App\Models\Coach::where('user_id', Auth::id())->first();
                $canManage = $coach && $training->sport->coach_id === $coach->id;
                if ($canManage) {
                    $coachRegistrations = $training->registrations()
                        ->with('user')
                        ->orderByDesc('created_at')
                        ->get();
                }
            }
        }

        return Inertia::render('Trainings/Show', [
            'training' => $training,
            'isRegistered' => $isRegistered,
            'canManage' => $canManage,
            'coachRegistrations' => $coachRegistrations,
        ]);
    }

    /**
     * Форма создания тренировки (админ)
     */
    public function create()
    {
        $sports = Sport::orderBy('name')->get();

        return Inertia::render('Trainings/Create', [
            'sports' => $sports,
        ]);
    }

    /**
     * Сохранение тренировки
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => ['required', 'exists:sports,id'],
            'date' => ['required', 'date'],
            'time' => ['required'],
            'place' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        Training::create($validated);

        return redirect()
            ->route('trainings.index')
            ->with('success', 'Тренування успішно додано');
    }
}
