<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    /** Sign in with University ID *or* e-mail + password. Repeated failures lock the login for a minute. */
    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($data['login']);
        $key   = Str::lower($login).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages(['login' => "Too many failed attempts. Please try again in {$seconds} seconds."]);
        }

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'university_id';

        if (Auth::attempt([$field => $login, 'password' => $data['password']], $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate(); // prevent session fixation

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors(['login' => 'The University ID / e-mail or password is incorrect.'])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
