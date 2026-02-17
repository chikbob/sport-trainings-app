<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RegistrationController extends Controller
{
    // Запись пользователя на тренировку
    public function register(Training $training)
    {
        $user = Auth::user();

        // Нельзя записаться на прошедшую тренировку
        if ($training->date < now()->toDateString()) {
            return back()->with('error', 'Неможливо записатися на минуле тренування.');
        }

        // Поиск существующей регистрации, включая cancelled
        $registration = Registration::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->first();

        if ($registration) {
            // Если запись уже есть и не отменена — нельзя повторно записываться
            if ($registration->status !== Registration::STATUS_CANCELLED) {
                return back()->with('error', 'Ви вже записані на це тренування.');
            }

            // Если запись есть, но отменена — обновляем статус на pending
            $registration->update([
                'status' => Registration::STATUS_PENDING,
            ]);

            return back()->with('success', 'Ви успішно повторно записались на тренування!');
        }

        // Если записи нет — создаём новую
        Registration::create([
            'user_id' => $user->id,
            'training_id' => $training->id,
            'status' => Registration::STATUS_PENDING,
        ]);

        return back()->with('success', 'Ви успішно записались на тренування!');
    }

    // Для повторной записи
    public function reRegister(Training $training)
    {
        $user = Auth::user();

        // Найти отменённую регистрацию для этого пользователя и тренировки
        $registration = Registration::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->where('status', Registration::STATUS_CANCELLED)
            ->first();

        if (!$registration) {
            return back()->with('error', 'Немає скасованої реєстрації для повторного запису.');
        }

        if ($training->date < now()->toDateString()) {
            return back()->with('error', 'Неможливо записатися на минуле тренування.');
        }

        // Обновляем статус на pending
        $registration->update(['status' => Registration::STATUS_PENDING]);

        return back()->with('success', 'Ви успішно повторно записались на тренування!');
    }

    // Профиль пользователя с его регистрациями
    public function profile()
    {
        $user = Auth::user();

        $registrations = Registration::with('training.sport')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Profile/Index', [
            'registrations' => $registrations,
        ]);
    }

    // Отмена регистрации
    public function cancel(Request $request, Registration $registration)
    {
        $user = Auth::user();

        if ($registration->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $registration->update([
            'status' => Registration::STATUS_CANCELLED,
        ]);

        return redirect()
            ->route('profile')
            ->with('success', 'Реєстрацію скасовано.');
    }
}
