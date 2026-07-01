<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecureTabSession
{
    /**
     * Handle an incoming request and secure all platform pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip this security check entirely during the login form submission process
        if ($request->routeIs('login') || $request->is('login*') || $request->is('test-vercel-db*')) {
            return $next($request);
        }

        // 🛡️ AIRTIGHT PORTAL WORKSPACE VERIFICATION GATE:
        if (Auth::check() && !$request->session()->has('tab_session_active')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')->withErrors([
                'session' => 'Security Notice: Your session has expired due to browser tab closure.'
            ]);
        }

        return $next($request);
    }
}
