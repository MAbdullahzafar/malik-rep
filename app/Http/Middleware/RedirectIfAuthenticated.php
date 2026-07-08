<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // This code must ONLY run if the user is verified as logged in!
            if (Auth::guard($guard)->check()) {
                
                // Check if they have the active tab token
                if (!$request->session()->has('tab_session_active')) {
                    
                    // Only wipe session data if we are NOT already on the login route to avoid infinite redirect loops
                    if (!$request->is('login')) {
                        Auth::guard($guard)->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return redirect()->route('login');
                    }
                }

                // If their session is valid, send them forward to the dashboard workspace home
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
