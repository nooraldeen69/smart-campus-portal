<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

/** AT-01 / REQ-01 / OBJ-1 - SSO login via University ID */
class SsoLoginTest extends PortalTestCase
{
    public function test_redirect_sends_user_to_idp_with_state_and_pkce(): void
    {
        $response = $this->get(route('sso.redirect'));

        $response->assertRedirectContains('https://idp.test/authorize');
        $response->assertRedirectContains('code_challenge_method=S256');
        $this->assertNotEmpty(session('sso.state'));
        $this->assertNotEmpty(session('sso.verifier'));
    }

    public function test_valid_callback_logs_the_user_in_and_creates_the_account(): void
    {
        Http::fake([
            'https://idp.test/token'    => Http::response(['access_token' => 'tok', 'token_type' => 'Bearer']),
            'https://idp.test/userinfo' => Http::response([
                'sub' => '20260001', 'university_id' => '20260001',
                'name' => 'Layla Hassan', 'email' => 'layla@example.edu',
            ]),
        ]);

        $verifier = str_repeat('v', 64);

        $this->withSession(['sso.state' => 'abc123', 'sso.verifier' => $verifier])
            ->get(route('sso.callback', ['code' => 'good-code', 'state' => 'abc123']))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['university_id' => '20260001', 'name' => 'Layla Hassan']);

        // PKCE verifier and auth code are forwarded to the token endpoint
        Http::assertSent(fn (Request $r) => $r->url() === 'https://idp.test/token'
            && $r['code'] === 'good-code'
            && $r['code_verifier'] === $verifier);
    }

    public function test_returning_user_is_not_duplicated(): void
    {
        User::factory()->create(['university_id' => '20260001', 'email' => 'layla@example.edu']);

        Http::fake([
            'https://idp.test/token'    => Http::response(['access_token' => 'tok']),
            'https://idp.test/userinfo' => Http::response([
                'university_id' => '20260001', 'name' => 'Layla H.', 'email' => 'layla@example.edu',
            ]),
        ]);

        $this->withSession(['sso.state' => 's', 'sso.verifier' => str_repeat('v', 64)])
            ->get(route('sso.callback', ['code' => 'c', 'state' => 's']));

        $this->assertSame(1, User::where('university_id', '20260001')->count());
    }

    public function test_state_mismatch_is_rejected_before_any_token_request(): void
    {
        Http::fake();

        $this->withSession(['sso.state' => 'expected', 'sso.verifier' => str_repeat('v', 64)])
            ->get(route('sso.callback', ['code' => 'x', 'state' => 'forged']))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        Http::assertNothingSent();
    }

    public function test_idp_error_shows_friendly_message(): void
    {
        Http::fake(['https://idp.test/token' => Http::response(['error' => 'invalid_grant'], 400)]);

        $this->withSession(['sso.state' => 's', 'sso.verifier' => str_repeat('v', 64)])
            ->get(route('sso.callback', ['code' => 'bad', 'state' => 's']))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error');

        $this->assertGuest();
    }

    public function test_logout_requires_post_and_ends_the_session(): void
    {
        $user = User::factory()->create(['university_id' => '20260001']);

        $this->actingAs($user)->get('/logout')->assertStatus(405);

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }
}
