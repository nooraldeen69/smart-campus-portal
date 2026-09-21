<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;

/** In-scope "Notifications" */
class NotificationsAndSyncTest extends PortalTestCase
{
    public function test_announcement_appears_on_dashboard_and_can_be_dismissed(): void
    {
        $user = $this->makeUser(['university_id' => '20260001']);

        $this->assertSame(0, Artisan::call('portal:announce', ['title' => 'Registration opens', 'body' => 'Monday 9am']));

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('Registration opens')
            ->assertSee('Monday 9am');

        $id = $user->unreadNotifications()->first()->id;
        $this->actingAs($user)->post(route('notifications.read', $id))->assertRedirect();

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_users_cannot_dismiss_other_peoples_notifications(): void
    {
        $owner = $this->makeUser(['university_id' => '1']);
        $other = $this->makeUser(['university_id' => '2']);
        $this->assertSame(0, Artisan::call('portal:announce', ['title' => 'T', 'body' => 'B']));
        $id = $owner->unreadNotifications()->first()->id;

        $this->actingAs($other)->post(route('notifications.read', $id));

        $this->assertSame(1, $owner->unreadNotifications()->count());
    }
}
