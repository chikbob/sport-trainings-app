<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function __construct()
    {
        // Только администратор может видеть список пользователей
        $this->middleware('admin')->only('index');
    }

    public function index(Request $request)
    {
        $users = User::with([
            'registrations.training.sport'
        ])
            ->search($request->search)
            ->role($request->role)
            ->orderBy(
                $request->sort ?? 'id',
                $request->direction ?? 'desc'
            )
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'sort', 'direction'])
        ]);
    }
}
