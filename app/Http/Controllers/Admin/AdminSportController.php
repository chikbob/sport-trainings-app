<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sport;
use App\Models\Coach;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSportController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Sports/Index', [
            'sports' => Sport::with('coach.user')
                ->orderBy('id', 'desc')
                ->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Sports/Create', [
            'coaches' => Coach::with('user')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'coach_id' => 'nullable|exists:coaches,id'
        ]);

        Sport::create($validated);

        return redirect()->route('admin.sports.index');
    }

    public function edit(Sport $sport)
    {
        return Inertia::render('Admin/Sports/Edit', [
            'sport' => $sport->load('coach'),
            'coaches' => Coach::with('user')->get()
        ]);
    }

    public function update(Request $request, Sport $sport)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'coach_id' => 'nullable|exists:coaches,id'
        ]);

        $sport->update($validated);

        return redirect()->route('admin.sports.index');
    }

    public function destroy(Sport $sport)
    {
        $sport->delete();
        return back();
    }
}
