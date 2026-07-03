<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Redirect path after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Special admin login
        if ($request->email === 'admin@school.com' && $request->password === 'malik12.') {

            $user = User::where('email', 'admin@school.com')->first();

            if ($user) {

                Auth::login($user, true);

                // Regenerate session for security
                $request->session()->regenerate();

                return redirect()->route('home');
            }
        }

        // Normal Laravel authentication
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], true)) {

            // Regenerate session
            $request->session()->regenerate();

            return redirect()->route('home');
        }

        // Failed login
        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}