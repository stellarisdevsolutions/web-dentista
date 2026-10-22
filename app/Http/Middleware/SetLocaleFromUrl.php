<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromUrl
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Explicit query parameter ?lang=en or ?lang=es
        if ($request->has('lang') && in_array($request->query('lang'), ['es', 'en'], true)) {
            $locale = (string) $request->query('lang');
            app()->setLocale($locale);
            session(['locale' => $locale]);

            return $next($request);
        }

        if ($request->has('locale') && in_array($request->query('locale'), ['es', 'en'], true)) {
            $locale = (string) $request->query('locale');
            app()->setLocale($locale);
            session(['locale' => $locale]);

            return $next($request);
        }

        $path = trim($request->path(), '/');

        // 2. English paths
        $isEnglishPath = $path === 'en'
            || str_starts_with($path, 'en/')
            || $path === 'services'
            || str_starts_with($path, 'services/')
            || $path === 'about'
            || $path === 'contact'
            || $path === 'schedule'
            || $path === 'privacy-policy'
            || $path === 'terms-and-conditions'
            || $path === 'patient-rights';

        // 3. Spanish paths
        $isSpanishPath = $path === 'es'
            || str_starts_with($path, 'es/')
            || $path === 'servicios'
            || str_starts_with($path, 'servicios/')
            || $path === 'nosotros'
            || $path === 'contacto'
            || $path === 'reservar'
            || $path === 'politica-de-privacidad'
            || $path === 'terminos-y-condiciones'
            || $path === 'derechos-del-paciente';

        if ($isEnglishPath) {
            app()->setLocale('en');
            session(['locale' => 'en']);
        } elseif ($isSpanishPath) {
            if (config('app.locale') !== 'en' || session('locale') === 'es') {
                app()->setLocale('es');
                session(['locale' => 'es']);
            }
        } elseif (session()->has('locale') && in_array(session('locale'), ['es', 'en'], true)) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}
