<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Redirect after login.
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
     * Show login page.
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

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            dd('❌ User not found in database');
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            dd('❌ Password is incorrect');
        }

        // Login user
        Auth::login($user, true);

        // Regenerate session
        $request->session()->regenerate();

        // TEMPORARY DEBUG
        dd([
            'Login Successful' => true,
            'Authenticated' => Auth::check(),
            'User ID' => Auth::id(),
            'User Email' => Auth::user()->email,
            'Session ID' => session()->getId(),
        ]);

        // Remove the dd() above later and uncomment this:
        // return redirect()->route('home');
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