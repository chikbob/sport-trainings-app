<?php

namespace App\Providers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (env('RENDER_EXTERNAL_URL')) {
            URL::forceRootUrl(env('RENDER_EXTERNAL_URL'));
        }

        if (! env('APP_URL') && env('RAILWAY_PUBLIC_DOMAIN')) {
            URL::forceRootUrl('https://' . env('RAILWAY_PUBLIC_DOMAIN'));
        }

        if (app()->environment('production') || env('RENDER') || env('RAILWAY_PUBLIC_DOMAIN')) {
            URL::forceScheme('https');
        }

        Inertia::share([
            'auth' => function () {
                return [
                    'user' => Auth::user() ? [
                        'id' => Auth::id(),
                        'name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                        'role' => Auth::user()->role,
                    ] : null
                ];
            },
            'appName' => config('app.name'),
            'currentRoute' => function () {
                // если используешь именованные маршруты — вернёт name
                return Route::currentRouteName();
            },
        ]);
    }
}
