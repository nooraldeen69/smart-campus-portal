<?php

namespace Tests\Feature;

class NetworkAccessTest extends PortalTestCase
{
    public function test_plain_http_is_not_redirected_to_https_unless_asked(): void
    {
        config(['services.campus.force_https' => 'false']);
        $this->get('http://192.168.1.50:8000/')->assertOk();

        config(['services.campus.force_https' => 'true']);
        $this->get('http://192.168.1.50:8000/')->assertRedirect('https://192.168.1.50:8000');
    }

    public function test_self_hosted_assets_exist_so_the_page_works_without_internet(): void
    {
        foreach (['bootstrap/bootstrap.min.css', 'bootstrap-icons/bootstrap-icons.min.css', 'bootstrap-icons/fonts/bootstrap-icons.woff2'] as $file) {
            $this->assertFileExists(public_path("vendor/{$file}"));
        }

        $this->get('/')->assertDontSee('cdn.jsdelivr.net', false);
    }
}
