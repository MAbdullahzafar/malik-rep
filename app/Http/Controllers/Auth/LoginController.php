<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
     * Forces the remember_me feature to remain strictly FALSE.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // 🌟 FORCE STRICT NON-PERSISTENT ATTRIBUTE MAP
        if ($this->guard()->attempt($this->credentials($request), false)) {
            return $this->sendLoginResponse($request);
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
            
            // 🛡️ BACKEND LOCK GATE CONNECTION:
            // This runs after session regeneration, ensuring the key persists across pages.
            $request->session()->put('tab_session_active', true);
        }

        return redirect()->intended($this->redirectPath());
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
