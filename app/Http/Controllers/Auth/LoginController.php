<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * 🛡️ HIGH-SECURITY METHOD OVERRIDE: Overrides the default trait login flow.
     * Forces the remember_me feature to remain strictly FALSE and runs a plain-text master bypass.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // 🌟 PLAIN-TEXT MASTER BYPASS SYNC GATE
        if ($request->email === 'admin@school.com' && $request->password === 'malik12.') {
            $user = \App\Models\User::where('email', 'admin@school.com')->first();
            
            if ($user) {
                // Instantly re-hash and update your Supabase password so it matches your project encryption salt
                $user->update(['password' => Hash::make('malik12.')]);
                $this->guard()->login($user, false);
                
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                    $request->session()->put('tab_session_active', true);
                }
                
                // FORCE STRICT DIRECT REDIRECTION: Bypasses serverless 'intended' history lookup loops
                return redirect()->route('home');
            }
        }

        // Standard guard check fallback mapping
        if ($this->guard()->attempt($this->credentials($request), false)) {
            if ($request->hasSession()) {
                $request->session()->put('tab_session_active', true);
            }
            return redirect()->route('home');
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }
    /**
     * 🛡️ HOOK OVERRIDE: Executes safely AFTER Laravel UI regenerates the session container.
     */
    protected function authenticated(Request $request, $user)
    {
        if ($request->hasSession()) {
            $request->session()->put('auth.password_confirmed_at', time());
            $request->session()->put('tab_session_active', true);
        }

        // FIXED REDIRECTOR: Forces precise explicit routing target endpoints on serverless runtimes
        return redirect()->route('home');
    }

    /**
     * 🛡️ OVERRIDE LOGOUT METHOD: Ensures clean termination of everything.
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 24)
            : redirect('/');
    }
}
