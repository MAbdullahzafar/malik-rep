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
        // 🛡️ AIRTIGHT PORTAL WORKSPACE VERIFICATION GATE:
        // If the user is logged in, but the session lacks our manual form login token,
        // it means they closed the browser tab/app and came back. Wipe everything instantly!
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
