<?php

namespace Tests\Feature;

use App\Jobs\SyncStudentGradesJob;
use App\Models\User;
use App\Services\CampusApiService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/** In-scope "Notifications" + RSK-05 grade-sync job */
class NotificationsAndSyncTest extends PortalTestCase
{
    public function test_announcement_appears_on_dashboard_and_can_be_dismissed(): void
    {
        $this->fakeCampusApis();
        $user = User::factory()->create(['university_id' => '20260001']);

        $this->artisan('portal:announce', ['title' => 'Registration opens', 'body' => 'Monday 9am'])
            ->assertSuccessful();

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('Registration opens')
            ->assertSee('Monday 9am');

        $id = $user->unreadNotifications()->first()->id;
        $this->actingAs($user)->post(route('notifications.read', $id))->assertRedirect();

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_users_cannot_dismiss_other_peoples_notifications(): void
    {
        $owner = User::factory()->create(['university_id' => '1']);
        $other = User::factory()->create(['university_id' => '2']);
        $this->artisan('portal:announce', ['title' => 'T', 'body' => 'B'])->assertSuccessful();
        $id = $owner->unreadNotifications()->first()->id;

        $this->actingAs($other)->post(route('notifications.read', $id));

        $this->assertSame(1, $owner->unreadNotifications()->count());
    }

    public function test_sync_job_notifies_when_a_new_grade_arrives(): void
    {
        $user = User::factory()->create(['university_id' => '20260001']);
        Cache::put(CampusApiService::gradesCacheKey('20260001'), [['course_name' => 'Databases', 'score' => '92']]);

        Http::fake(['https://sis.test/grades/*' => Http::response([
            ['course_name' => 'Databases', 'score' => '92'],
            ['course_name' => 'Networks', 'score' => '88'],
        ])]);

        (new SyncStudentGradesJob($user))->handle(app(CampusApiService::class));

        $this->assertSame(1, $user->unreadNotifications()->count());
        $this->assertStringContainsString('Networks: 88', $user->unreadNotifications()->first()->data['body']);
    }

    public function test_sync_job_throws_on_sis_failure_so_the_queue_retries(): void
    {
        $user = User::factory()->create(['university_id' => '20260001']);
        Http::fake(['https://sis.test/grades/*' => Http::response('down', 503)]);

        $this->expectException(\Illuminate\Http\Client\RequestException::class);

        (new SyncStudentGradesJob($user))->handle(app(CampusApiService::class));
    }
}
