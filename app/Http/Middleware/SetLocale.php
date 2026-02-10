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
        // Get locale from input (query or body) - priority 1
        $locale = $request->input('lang');
        
        // If not in input, check custom header 'lang' - priority 2
        if (!$locale) {
            $locale = $request->header('lang');
        }

        // If not in custom header, check Accept-Language header - priority 3
        if (!$locale && $request->header('Accept-Language')) {
            $locale = $request->getPreferredLanguage(['ar', 'en']);
        }
        
        // If still not set, check session
        if (!$locale && $request->hasSession()) {
            $locale = $request->session()->get('locale');
        }
        
        // Validate locale or fall back to default
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

