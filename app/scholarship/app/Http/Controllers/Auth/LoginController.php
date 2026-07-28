<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Handles authenticating users into the admin panel.
 *
 * Laravel's AuthenticatesUsers trait was removed from the framework core after 5.8,
 * so the login/logout flow this controller used to inherit is now spelled out here.
 * Behaviour is unchanged: the single "email" field accepts either an email address
 * or a username, and a successful login lands on /admin.
 */
class LoginController extends Controller
{
    /**
     * Where to redirect users after login.
     */
    protected string $redirectTo = '/admin';

    /**
     * Max failed attempts before throttling, and the lockout window in minutes.
     */
    protected int $maxAttempts = 5;

    protected int $decayMinutes = 1;

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited($request);

        if (Auth::attempt($this->credentials($request), $request->boolean('remember'))) {
            RateLimiter::clear($this->throttleKey($request));
            $request->session()->regenerate();

            return redirect()->intended($this->redirectTo);
        }

        RateLimiter::hit($this->throttleKey($request), $this->decayMinutes * 60);

        // Preserved from the original controller: failures flash a "fail" message,
        // which is what the login view renders.
        return redirect()->route('login')
            ->withInput($request->only('email', 'remember'))
            ->with('fail', 'You have entered wrong credentials, please try again.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Resolve credentials, letting the single form field serve as email or username.
     */
    protected function credentials(Request $request): array
    {
        $login = $request->input('email');

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $login,
            'password' => $request->input('password'),
        ];
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), $this->maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email').'|'.$request->ip()));
    }
}
