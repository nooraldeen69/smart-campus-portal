<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Http;

/** AT-02 (grades from SIS), AT-03 (<2 s), AT-06 (mobile layout) */
class DashboardTest extends PortalTestCase
{
    private function student(): User
    {
        return User::factory()->create(['university_id' => '20260001']);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    // ---- AT-02 / REQ-02 ----
    public function test_dashboard_aggregates_sis_lms_and_library(): void
    {
        $this->fakeCampusApis();

        $this->actingAs($this->student())->get('/dashboard')
            ->assertOk()
            ->assertSee('Databases')            // SIS grade
            ->assertSee('IT301')                // LMS course
            ->assertSee('Clean Code');          // Library loan
    }

    // ---- AT-03 / REQ-03 / OBJ-2 ----
    public function test_second_load_is_served_from_cache_and_is_fast(): void
    {
        $this->fakeCampusApis();
        $student = $this->student();

        $this->actingAs($student)->get('/dashboard')->assertOk();
        $second = $this->actingAs($student)->get('/dashboard')->assertOk();

        Http::assertSentCount(3); // 1 call per back-end, none on the 2nd load

        preg_match('/dur=([\d.]+)/', $second->headers->get('Server-Timing'), $m);
        $this->assertLessThan(2000, (float) $m[1], 'Dashboard must render in under 2 seconds');
    }

    public function test_api_failures_are_not_cached(): void
    {
        $this->fakeCampusApis([
            'https://sis.test/grades/*' => Http::sequence()
                ->push('boom', 500)
                ->push([['course_name' => 'Databases', 'score' => '92']]),
        ]);
        $student = $this->student();

        $this->actingAs($student)->get('/dashboard')
            ->assertOk()
            ->assertSee('temporarily unavailable');

        // SIS recovered - grades must appear immediately, not after the 1h cache expires
        $this->actingAs($student)->get('/dashboard')
            ->assertOk()
            ->assertSee('Databases');
    }

    public function test_stale_data_is_served_when_a_backend_goes_down(): void
    {
        $this->fakeCampusApis([
            'https://sis.test/grades/*' => Http::sequence()
                ->push([['course_name' => 'Databases', 'score' => '92']])
                ->push('down', 503),
        ]);
        $student = $this->student();

        $this->actingAs($student)->get('/dashboard')->assertSee('Databases');

        \Illuminate\Support\Facades\Cache::forget('sis_grades_20260001'); // simulate TTL expiry only

        $this->actingAs($student)->get('/dashboard')
            ->assertOk()
            ->assertSee('Databases')
            ->assertSee('last known data');
    }

    public function test_unsafe_lms_links_are_neutralised(): void
    {
        $this->fakeCampusApis([
            'https://lms.test/courses/*' => Http::response([[
                'course_code' => 'X', 'title' => 'Evil', 'lms_link' => 'javascript:alert(1)',
            ]]),
        ]);

        $this->actingAs($this->student())->get('/dashboard')
            ->assertOk()
            ->assertDontSee('javascript:alert', false);
    }

    // ---- AT-06 / AC-03 ----
    public function test_layout_is_mobile_first(): void
    {
        $this->fakeCampusApis();

        $this->actingAs($this->student())->get('/dashboard')
            ->assertSee('name="viewport" content="width=device-width, initial-scale=1"', false)
            ->assertSee('container-fluid', false)
            ->assertSee('col-12 col-md-6', false);
    }
}
