<?php

namespace Tests\Feature;

/** Supports AT-04 / QM-02 / RSK-02 (automated baseline checks; a real pen-test is still required). */
class SecurityTest extends PortalTestCase
{
    public function test_hardening_headers_are_present(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_hsts_is_sent_over_https(): void
    {
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security');
    }

    public function test_health_endpoint_is_up_for_uptime_monitoring(): void
    {
        $this->get('/up')->assertOk(); // QM-03 / AT-05 monitor target
    }
}
