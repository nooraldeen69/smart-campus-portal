<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class AnnounceCommand extends Command
{
    protected $signature = 'portal:announce {title : Short headline} {body : Message text}';

    protected $description = 'Send an in-portal notification to every user';

    public function handle(): int
    {
        $count = 0;

        User::query()->chunkById(200, function ($users) use (&$count) {
            Notification::send($users, new AnnouncementNotification($this->argument('title'), $this->argument('body')));
            $count += $users->count();
        });

        $this->info("Announcement sent to {$count} user(s).");

        return self::SUCCESS;
    }
}
