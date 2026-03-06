<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CoachRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $coach = Coach::where('user_id', Auth::id())->firstOrFail();

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

        return Inertia::render('Coach/Registrations/Index', [
            'registrations' => $registrations,
            'filters' => $request->only(['status']),
        ]);
    }

    public function update(Request $request, Registration $registration)
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
