<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/** In-portal notification (In-Scope: "Notifications"). Stored in the `notifications` table. */
class AnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(public string $title, public string $body)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body'  => $this->body,
        ];
    }
}
