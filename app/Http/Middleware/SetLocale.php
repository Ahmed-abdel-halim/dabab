<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from query parameter (priority)
        $locale = $request->query('lang');
        
        // If not in query, check Accept-Language header
        if (!$locale) {
            $locale = $request->getPreferredLanguage(['ar', 'en']);
        }
        
        // If still not set, check session
        if (!$locale && $request->hasSession()) {
            $locale = $request->session()->get('locale');
        }
        
        // Validate locale and set default
        $supportedLocales = ['ar', 'en'];
        if (!$locale || !in_array($locale, $supportedLocales)) {
            $locale = config('app.locale', 'en');
        }
        
        // Set the application locale explicitly
        App::setLocale($locale);
        
        // Also set it in the request for consistency
        $request->setLocale($locale);
        
        // Store in session for future requests
        if ($request->hasSession()) {
            $request->session()->put('locale', $locale);
        }
        
        return $next($request);
    }
}

