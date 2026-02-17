<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\Sport;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminTrainingController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Trainings/Index', [
            'trainings' => Training::with('sport')
                ->orderBy('id', 'desc')
                ->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Trainings/Create', [
            'sports' => Sport::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'date' => 'required|date',
            'time' => 'required',
            'place' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Training::create($validated);

        return redirect()->route('admin.trainings.index');
    }

    public function edit(Training $training)
    {
        return Inertia::render('Admin/Trainings/Edit', [
            'training' => $training,
            'sports' => Sport::all()
        ]);
    }

    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'sport_id' => 'required|exists:sports,id',
            'date' => 'required|date',
            'time' => 'required',
            'place' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $training->update($validated);

        return redirect()->route('admin.trainings.index');
    }

    public function destroy(Training $training)
    {
        $training->delete();
        return back();
    }
}
