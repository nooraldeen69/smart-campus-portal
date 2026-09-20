<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\AnnouncementNotification;
use App\Services\CampusApiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * RSK-05 mitigation: background grade sync with automated retry + back-off.
 * Notifies the student when a new grade appears.
 */
class SyncStudentGradesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public bool $deleteWhenMissingModels = true;

    public function __construct(public User $user)
    {
    }

    /** Seconds to wait before each retry. */
    public function backoff(): array
    {
        return [30, 60, 120, 300];
    }

    public function handle(CampusApiService $api): void
    {
        $studentId = $this->user->university_id;
        $before    = Cache::get(CampusApiService::gradesCacheKey($studentId));

        $after = $api->refreshGrades($studentId); // throws => job is retried

        if (is_array($before)) {
            $new = array_diff($this->fingerprints($after), $this->fingerprints($before));

            if ($new !== []) {
                $this->user->notify(new AnnouncementNotification('New grade posted', implode(', ', $new)));
            }
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error("Grade sync permanently failed for {$this->user->university_id}: {$e->getMessage()}");
    }

    /** @return string[] */
    private function fingerprints(array $grades): array
    {
        return array_values(array_map(
            fn ($g) => (is_array($g) ? ($g['course_name'] ?? '?').': '.($g['score'] ?? '?') : '?'),
            $grades,
        ));
    }
}
