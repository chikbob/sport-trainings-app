<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCoachController extends Controller
{
    public function index()
    {
        $coaches = Coach::with('user')->paginate(10);

        return Inertia::render('Admin/Coaches/Index', [
            'coaches' => $coaches
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Coaches/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
            'coach' => $coach
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
