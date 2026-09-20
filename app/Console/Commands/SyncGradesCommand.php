<?php

namespace App\Console\Commands;

use App\Jobs\SyncStudentGradesJob;
use App\Models\User;
use Illuminate\Console\Command;

class SyncGradesCommand extends Command
{
    protected $signature = 'portal:sync-grades';

    protected $description = 'Queue a SIS grade refresh (with automatic retry) for every portal user';

    public function handle(): int
    {
        $count = 0;

        User::whereNotNull('university_id')->chunkById(100, function ($users) use (&$count) {
            foreach ($users as $user) {
                SyncStudentGradesJob::dispatch($user);
                $count++;
            }
        });

        $this->info("Queued {$count} grade sync job(s).");

        return self::SUCCESS;
    }
}
