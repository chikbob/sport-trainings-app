<?php

use App\Http\Controllers\Admin\AdminCoachController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\Admin\AdminSportController;
use App\Http\Controllers\Admin\AdminTrainingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Models\Registration;
use App\Models\Sport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

//
// ГЛАВНАЯ СТРАНИЦА
//
Route::get('/', function () {
    $user = Auth::user();

    if ($user) {
        $nextRegistration = Registration::with('training.sport')
            ->where('user_id', $user->id)
            ->where('status', '!=', \App\Models\Registration::STATUS_CANCELLED)
            ->whereHas('training', function ($query) {
                $query->whereDate('date', '>=', Carbon::today());
            })
            ->join('trainings', 'registrations.training_id', '=', 'trainings.id')
            ->orderBy('trainings.date', 'asc')
            ->select('registrations.*')
            ->first();

        return Inertia::render('Home', [
            'nextRegistration' => $nextRegistration,
        ]);
    }

    $latestSports = Sport::orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

    return Inertia::render('Home', [
        'latestSports' => $latestSports,
    ]);
})->name('home');


//
// АУТЕНТИФИКАЦИЯ
//
Route::get('/register', [AuthController::class, 'registerPage'])->name('register.page');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/login', [AuthController::class, 'loginPage'])->name('login.page');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//
// ПРОФИЛЬ И РЕГИСТРАЦИИ
//
Route::middleware('auth')->group(function () {

    Route::get('/profile', [RegistrationController::class, 'profile'])
        ->name('profile');

    Route::delete('/registrations/{registration}/cancel', [RegistrationController::class, 'cancel'])
        ->name('registrations.cancel');

    Route::post('/trainings/{training}/register', [RegistrationController::class, 'register'])
        ->name('trainings.register');

    Route::post('/trainings/{training}/reregister', [RegistrationController::class, 'reRegister'])
        ->name('trainings.reregister');
});


//
// СПОРТ И ТРЕНИРОВКИ (для всех)
//
Route::resource('sports', SportController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'sports.index',
        'show' => 'sports.show',
    ]);

Route::resource('trainings', TrainingController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'trainings.index',
        'show' => 'trainings.show',
    ]);

//
// АДМИН-ПАНЕЛЬ
//
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard', [
                'stats' => [
                    'users' => \App\Models\User::count(),
                    'sports' => \App\Models\Sport::count(),
                    'trainings' => \App\Models\Training::count(),
                ]
            ]);
        })->name('dashboard');

        Route::resource('sports', AdminSportController::class);
        Route::resource('trainings', AdminTrainingController::class);
        Route::resource('users', AdminUserController::class);

        // Добавляем ресурсный маршрут для coaches
        Route::resource('coaches', AdminCoachController::class);
    });
