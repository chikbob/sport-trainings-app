<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->cookie('lang') ?? $request->header('X-Locale');
        if (in_array($lang, ['ru', 'uk', 'en'], true)) {
            App::setLocale($lang);
        }

        return $next($request);
    }
}
