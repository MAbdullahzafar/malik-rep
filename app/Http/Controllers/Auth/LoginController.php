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

    // Check password
    if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        dd('❌ Password is incorrect');
    }

    // Login user
    Auth::login($user, true);

    // Regenerate session
    $request->session()->regenerate();

    // Debug authentication
    dd([
        '✅ Login Successful' => true,
        'Authenticated' => Auth::check(),
        'User ID' => Auth::id(),
        'User Email' => Auth::user()->email,
        'Session ID' => session()->getId(),
    ]);

    // Uncomment these after debugging
    // return redirect()->route('home');
}