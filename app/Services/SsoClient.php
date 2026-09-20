<?php

namespace App\Services;

use App\Exceptions\SsoException;
use Illuminate\Support\Facades\Http;

/**
 * Minimal OAuth2 Authorization-Code client with PKCE (S256) - WBS 1.2.1 / REQ-01.
 * Talks to the university Identity Provider; all endpoints come from config/services.php.
 */
class SsoClient
{
    public function authorizationUrl(string $state, string $codeChallenge): string
    {
        $cfg = config('services.sso');

        return $cfg['authorize_url'].'?'.http_build_query([
            'response_type'         => 'code',
            'client_id'             => $cfg['client_id'],
            'redirect_uri'          => $cfg['redirect'],
            'scope'                 => $cfg['scopes'],
            'state'                 => $state,
            'code_challenge'        => $codeChallenge,
            'code_challenge_method' => 'S256',
        ]);
    }

    /** RFC 7636: BASE64URL(SHA256(verifier)) */
    public static function challengeFor(string $verifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');
    }

    /** Exchange the authorization code for an access token. */
    public function exchangeCode(string $code, string $verifier): string
    {
        $cfg = config('services.sso');

        $token = Http::asForm()->acceptJson()->timeout(5)
            ->post($cfg['token_url'], [
                'grant_type'    => 'authorization_code',
                'code'          => $code,
                'redirect_uri'  => $cfg['redirect'],
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'code_verifier' => $verifier,
            ])
            ->throw()
            ->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new SsoException('Token response did not contain an access_token.');
        }

        return $token;
    }

    /**
     * Fetch the user's profile and normalise it.
     *
     * @return array{university_id:string,name:string,email:string}
     */
    public function fetchProfile(string $accessToken): array
    {
        $profile = Http::withToken($accessToken)->acceptJson()->timeout(5)
            ->get(config('services.sso.userinfo_url'))
            ->throw()
            ->json();

        $id = is_array($profile) ? ($profile['university_id'] ?? $profile['sub'] ?? null) : null;

        if (! is_string($id) && ! is_int($id)) {
            throw new SsoException('Profile did not contain a university_id / sub claim.');
        }

        $id = (string) $id;

        return [
            'university_id' => $id,
            'name'          => (string) ($profile['name'] ?? $id),
            'email'         => (string) ($profile['email'] ?? "{$id}@sso.invalid"),
        ];
    }
}
