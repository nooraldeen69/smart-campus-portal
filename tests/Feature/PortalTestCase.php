<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Shared setup: fake endpoints so tests never touch a real IdP/SIS/LMS. */
abstract class PortalTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.sso.client_id'     => 'portal-test',
            'services.sso.client_secret' => 'secret',
            'services.sso.redirect'      => 'http://localhost/auth/sso/callback',
            'services.sso.authorize_url' => 'https://idp.test/authorize',
            'services.sso.token_url'     => 'https://idp.test/token',
            'services.sso.userinfo_url'  => 'https://idp.test/userinfo',
            'services.sso.logout_url'    => null,
            'services.sis.endpoint'      => 'https://sis.test',
            'services.lms.endpoint'      => 'https://lms.test',
            'services.library.endpoint'  => 'https://library.test',
        ]);
    }

    protected function fakeCampusApis(array $overrides = []): void
    {
        Http::fake($overrides + [
            'https://sis.test/grades/*'     => Http::response([['course_name' => 'Databases', 'score' => '92']]),
            'https://lms.test/courses/*'    => Http::response([[
                'course_code' => 'IT301', 'title' => 'Networks', 'lms_link' => 'https://lms.test/c/301',
            ]]),
            'https://library.test/loans/*'  => Http::response([['title' => 'Clean Code', 'due_date' => '2026-10-01']]),
        ]);
    }
}
