<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

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
     * Forces the remember_me feature to remain strictly FALSE and injects active tab session markers.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // 🌟 FORCE STRICT NON-PERSISTENT ATTRIBUTE MAP
        // The third parameter is the remember flag; we hardcode it to FALSE
        if ($this->guard()->attempt($this->credentials($request), false)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
                
                // 🛡️ BACKEND LOCK GATE CONNECTION: 
                // Injects the verification tag into the session ONLY on a successful manual login attempt!
                $request->session()->put('tab_session_active', true);
            }

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
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
