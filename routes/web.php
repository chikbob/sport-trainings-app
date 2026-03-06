<?php

use App\Http\Controllers\Admin\AdminCoachController;
use App\Http\Controllers\Admin\AdminRegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SportController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\Admin\AdminSportController;
use App\Http\Controllers\Admin\AdminTrainingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Coach\CoachDashboardController;
use App\Http\Controllers\Coach\CoachTrainingController;
use App\Http\Controllers\Coach\CoachRegistrationController;
use App\Models\Registration;
use App\Models\Sport;
use App\Models\Training;
use App\Models\User;
use App\Models\Coach;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    Route::get('/users', [ParticipantController::class, 'index'])
        ->name('users.index');
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
                    'users' => User::count(),
                    'sports' => Sport::count(),
                    'trainings' => Training::count(),
                    'registrations' => Registration::count(),
                    'coaches' => Coach::count(),
                ],
                'recentUsers' => User::orderByDesc('created_at')
                    ->limit(5)
                    ->get(['id', 'name', 'email', 'role', 'created_at']),
                'upcomingTrainings' => Training::with('sport')
                    ->whereDate('date', '>=', Carbon::today())
                    ->orderBy('date')
                    ->limit(5)
                    ->get(['id', 'sport_id', 'date', 'time', 'place']),
                'registrationsByStatus' => Registration::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->get(),
            ]);
        })->name('dashboard');

        Route::resource('sports', AdminSportController::class);
        Route::resource('trainings', AdminTrainingController::class);
        Route::resource('users', AdminUserController::class);

        // Добавляем ресурсный маршрут для coaches
        Route::resource('coaches', AdminCoachController::class);

        Route::resource('registrations', AdminRegistrationController::class)
            ->only(['index', 'edit', 'update']);
    });

//
// КАБИНЕТ ТРЕНЕРА
//
Route::middleware(['auth', 'coach'])
    ->prefix('coach')
    ->name('coach.')
    ->group(function () {
        Route::get('/', [CoachDashboardController::class, 'index'])->name('dashboard');
        Route::post('/trainings/{training}/cancel', [CoachTrainingController::class, 'cancel'])
            ->name('trainings.cancel');
        Route::post('/trainings/{training}/complete', [CoachTrainingController::class, 'complete'])
            ->name('trainings.complete');
        Route::resource('trainings', CoachTrainingController::class)
            ->only(['index', 'edit', 'update'])
            ->names([
                'index' => 'trainings.index',
                'edit' => 'trainings.edit',
                'update' => 'trainings.update',
            ]);
        Route::resource('registrations', CoachRegistrationController::class)
            ->only(['index', 'update'])
            ->names([
                'index' => 'registrations.index',
                'update' => 'registrations.update',
            ]);
    });
