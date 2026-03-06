<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $registrations = Registration::with(['user', 'training.sport'])
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->orWhereHas('training.sport', function ($sport) use ($search) {
                        $sport->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Registrations/Index', [
            'registrations' => $registrations,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function edit(Registration $registration)
    {
        $registration->load(['user', 'training.sport']);

        return Inertia::render('Admin/Registrations/Edit', [
            'registration' => $registration,
        ]);
    }

    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,cancelled,rejected,attended,no_show',
        ]);

        $registration->update($validated);

        return redirect()->route('admin.registrations.index');
    }
}
