<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCoachController extends Controller
{
    public function index(Request $request)
    {
        $coaches = Coach::with('user')
            ->when($request->search, function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('phone', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($user) use ($search) {
                            $user->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Coaches/Index', [
            'coaches' => $coaches,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Coaches/Create', [
            'coachUsers' => User::where('role', 'coach')
                ->whereDoesntHave('coach')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:coaches,user_id',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'specialization' => 'nullable|string|max:255',
        ]);

        Coach::create($validated);

        return redirect()->route('admin.coaches.index');
    }

    public function edit(Coach $coach)
    {
        return Inertia::render('Admin/Coaches/Edit', [
            'coach' => $coach->load('user')
        ]);
    }

    public function update(Request $request, Coach $coach)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'specialization' => 'nullable|string|max:255',
        ]);

        $coach->update($validated);

        return redirect()->route('admin.coaches.index');
    }

    public function destroy(Coach $coach)
    {
        $coach->delete();
        return back();
    }
}
