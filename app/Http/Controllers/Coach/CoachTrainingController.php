<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Registration;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CoachTrainingController extends Controller
{
    public function index(Request $request)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();
        $filter = $request->input('filter', 'upcoming');

        $query = Training::with('sport')
            ->whereHas('sport', function ($q) use ($coach) {
                $q->where('coach_id', $coach->id);
            });

        if ($filter === 'past') {
            $query->whereDate('date', '<', now()->toDateString())
                ->where('is_completed', false)
                ->where('is_cancelled', false);
        } elseif ($filter === 'archive') {
            $query->where(function ($q) {
                $q->where('is_completed', true)
                    ->orWhere('is_cancelled', true);
            });
        } else {
            $query->whereDate('date', '>=', now()->toDateString())
                ->where('is_completed', false)
                ->where('is_cancelled', false);
        }

        $trainings = $query
            ->orderBy('date')
            ->orderBy('time')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Coach/Trainings/Index', [
            'trainings' => $trainings,
            'filters' => ['filter' => $filter],
        ]);
    }

    public function edit(Training $training)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();
        if ($training->sport->coach_id !== $coach->id) {
            abort(403);
        }

        return Inertia::render('Coach/Trainings/Edit', [
            'training' => $training->load('sport'),
        ]);
    }

    public function update(Request $request, Training $training)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();
        if ($training->sport->coach_id !== $coach->id) {
            abort(403);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'place' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $training->update($validated);

        return redirect()->route('coach.trainings.index');
    }

    public function cancel(Training $training)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();
        if ($training->sport->coach_id !== $coach->id) {
            abort(403);
        }

        DB::transaction(function () use ($training) {
            $training->update([
                'is_cancelled' => true,
                'cancelled_at' => now(),
            ]);

            $training->registrations()
                ->where('status', '!=', Registration::STATUS_CANCELLED)
                ->update(['status' => Registration::STATUS_CANCELLED]);
        });

        return back();
    }

    public function complete(Training $training)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();
        if ($training->sport->coach_id !== $coach->id) {
            abort(403);
        }

        DB::transaction(function () use ($training) {
            $training->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            $training->registrations()
                ->whereIn('status', [Registration::STATUS_PENDING, Registration::STATUS_APPROVED])
                ->update(['status' => Registration::STATUS_ATTENDED]);

            $training->registrations()
                ->whereIn('status', [Registration::STATUS_CANCELLED, Registration::STATUS_REJECTED])
                ->update(['status' => Registration::STATUS_NO_SHOW]);
        });

        return back();
    }
}
