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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$guards
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                
                // 🛡️ AIRTIGHT BACKEND PROTECTION GATEWAY
                // If a user has a logged-in session, but they DO NOT have our manual login token,
                // it means the browser window was closed and reopened. Wipe the session instantly!
                if (!$request->session()->has('tab_session_active')) {
                    Auth::guard($guard)->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return redirect()->route('login');
                }

                // If they logged in using the form, let them pass safely to the dashboard
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
