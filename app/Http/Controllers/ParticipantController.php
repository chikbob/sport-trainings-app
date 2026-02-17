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

    public function index()
    {
        // Загружаем всех юзеров с их регистрациями
        $users = User::with([
            'registrations.training.sport'
        ])->orderBy('id', 'desc')->get();

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }
}
