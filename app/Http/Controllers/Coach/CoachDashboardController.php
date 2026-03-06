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

class CoachDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $coach = Coach::where('user_id', $user->id)->firstOrFail();

        $upcomingTrainings = Training::with(['sport', 'registrations.user'])
            ->whereHas('sport', function ($q) use ($coach) {
                $q->where('coach_id', $coach->id);
            })
            ->whereDate('date', '>=', now()->toDateString())
            ->where('is_cancelled', false)
            ->where('is_completed', false)
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $registrations = Registration::with(['user', 'training.sport'])
            ->whereHas('training.sport', function ($q) use ($coach) {
                $q->where('coach_id', $coach->id);
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'trainings' => $upcomingTrainings->count(),
            'registrations' => Registration::whereHas('training.sport', function ($q) use ($coach) {
                $q->where('coach_id', $coach->id);
            })->count(),
            'attended' => Training::whereHas('sport', function ($q) use ($coach) {
                $q->where('coach_id', $coach->id);
            })->where('is_completed', true)->count(),
        ];

        return Inertia::render('Coach/Dashboard', [
            'coach' => $coach->load('user'),
            'upcomingTrainings' => $upcomingTrainings,
            'registrations' => $registrations,
            'stats' => $stats,
            'filters' => $request->only(['status']),
        ]);
    }

    public function cancelTraining(Training $training)
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

        return back()->with('success', 'Training cancelled');
    }

    public function updateRegistrationStatus(Request $request, Registration $registration)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();

        if ($registration->training->sport->coach_id !== $coach->id) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,cancelled,rejected,attended,no_show',
        ]);

        $registration->update($validated);

        return back();
    }
}
