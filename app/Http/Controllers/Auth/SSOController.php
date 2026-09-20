<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\SsoException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoClient;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SSOController extends Controller
{
    private const LOGIN_FAILED = 'Sign-in failed. Please try again or contact the IT helpdesk.';

    /** Step 1: send the browser to the university Identity Provider. */
    public function redirect(Request $request, SsoClient $sso): RedirectResponse
    {
        $state    = Str::random(40); // CSRF protection for the OAuth flow
        $verifier = Str::random(64); // PKCE code verifier

        $request->session()->put([
            'sso.state'    => $state,
            'sso.verifier' => $verifier,
        ]);

        return redirect()->away($sso->authorizationUrl($state, SsoClient::challengeFor($verifier)));
    }

    /** Step 2: the IdP sends the user back with ?code=...&state=... */
    public function callback(Request $request, SsoClient $sso): RedirectResponse
    {
        $state    = $request->session()->pull('sso.state');
        $verifier = $request->session()->pull('sso.verifier');

        if ($request->filled('error')) {
            return $this->fail('Sign-in was cancelled or denied.');
        }

        if (! $state || ! $verifier || ! hash_equals((string) $state, (string) $request->query('state'))) {
            return $this->fail('Your sign-in session expired. Please try again.');
        }

        if (! $request->filled('code')) {
            return $this->fail(self::LOGIN_FAILED);
        }

        try {
            $token   = $sso->exchangeCode((string) $request->query('code'), (string) $verifier);
            $profile = $sso->fetchProfile($token);
        } catch (SsoException|HttpClientException $e) {
            Log::warning('SSO login failed: '.$e->getMessage());

            return $this->fail(self::LOGIN_FAILED);
        }

        // Just-in-time provisioning keyed on the University ID (REQ-01)
        $user = User::updateOrCreate(
            ['university_id' => $profile['university_id']],
            ['name' => $profile['name'], 'email' => $profile['email']],
        );

        Auth::login($user);
        $request->session()->regenerate(); // prevent session fixation

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Optional single-logout at the Identity Provider
        if ($logoutUrl = config('services.sso.logout_url')) {
            return redirect()->away($logoutUrl.'?'.http_build_query([
                'post_logout_redirect_uri' => url('/'),
            ]));
        }

        return redirect('/');
    }

    private function fail(string $message): RedirectResponse
    {
        return redirect()->route('login')->with('error', $message);
    }
}
