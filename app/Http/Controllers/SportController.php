<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SportController extends Controller
{
    public function __construct()
    {
        // Только админ может создавать секции
        $this->middleware('admin')->only(['create', 'store']);
    }

    public function index()
    {
        $sports = Sport::withCount('trainings')
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', '!=', Registration::STATUS_CANCELLED)
                        ->distinct('user_id');
                }
            ])
            // Подгружаем тренировки, только с датой >= сегодня
            ->with(['trainings' => function ($query) {
                $query->whereDate('date', '>=', now()->toDateString());
            }])
            ->get()
            // Фильтруем виды спорта, у которых есть хотя бы одна актуальная тренировка
            ->filter(function ($sport) {
                return $sport->trainings->isNotEmpty();
            })
            ->values();

        return Inertia::render('Sports/Index', [
            'sports' => $sports,
        ]);
    }

    public function create()
    {
        return Inertia::render('Sports/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'coach_name'  => ['nullable', 'string', 'max:255'],
            'location'    => ['nullable', 'string', 'max:255'],
        ]);

        Sport::create($validated);

        return redirect()
            ->route('sports.index')
            ->with('success', 'Секцію успішно додано!');
    }

    public function show(Sport $sport)
    {
        $sport->load(['trainings' => function ($query) {
            // Только тренировки с датой сегодня или позже
            $query->whereDate('date', '>=', now()->toDateString())
                ->orderBy('date');
        }]);

        $userId = Auth::id();

        $sport->trainings->transform(function ($training) use ($userId) {
            $training->isRegistered = false;

            if ($userId) {
                $training->isRegistered = $training->registrations()
                    ->where('user_id', $userId)
                    ->where('status', '!=', Registration::STATUS_CANCELLED)
                    ->exists();
            }

            return $training;
        });

        return Inertia::render('Sports/Show', [
            'sport' => $sport,
        ]);
    }
}
