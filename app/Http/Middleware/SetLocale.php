<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = array_keys(config('app.available_locales', []));
        $defaultLocale = config('app.locale');

        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! in_array($locale, $supportedLocales, true)) {
            $locale = $request->getPreferredLanguage($supportedLocales) ?: $defaultLocale;
        }

        if (! is_string($locale) || ! in_array($locale, $supportedLocales, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
