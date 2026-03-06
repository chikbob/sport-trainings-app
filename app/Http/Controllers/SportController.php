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

    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'asc');
        $sort = $sort === 'desc' ? 'desc' : 'asc';

        $sports = Sport::query()
            ->with('coach.user')
            ->withCount('trainings')
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', '!=', Registration::STATUS_CANCELLED)
                        ->distinct('user_id');
                }
            ])
            ->whereHas('trainings', function ($query) {
                $query->whereDate('date', '>=', now()->toDateString())
                    ->where('is_cancelled', false);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->orderBy('name', $sort)
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Sports/Index', [
            'sports' => $sports,
            'filters' => [
                'search' => $search,
                'sort' => $sort,
            ],
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
        $sport->load(['coach.user', 'trainings' => function ($query) {
            // Только тренировки с датой сегодня или позже
            $query->whereDate('date', '>=', now()->toDateString())
                ->where('is_cancelled', false)
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
