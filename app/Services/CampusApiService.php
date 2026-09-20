<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * API Gateway service layer (WBS 1.3.2).
 *
 * Fetches data from the legacy SIS, LMS and Library systems.
 *  - Responses are cached to meet the sub-2-second load objective (OBJ-2).
 *  - Failures are NEVER cached (otherwise one SIS blip would hide grades for an hour).
 *  - A long-lived "stale" copy is served if a back-end is down (supports OBJ-4 uptime),
 *    and the dashboard is told which sources are degraded via degradedSources().
 */
class CampusApiService
{
    private const STALE_TTL = 86400; // 24 h

    /** @var array<string,string> source => 'stale'|'unavailable' */
    private array $degraded = [];

    /** Fetch grades from legacy SIS (REQ-02) */
    public function getStudentGrades($studentId): array
    {
        return $this->remember('sis', self::gradesCacheKey($studentId), $this->url('sis', "grades/{$studentId}"));
    }

    /** Fetch active courses from the LMS */
    public function getActiveLMSCourses($studentId): array
    {
        $courses = $this->remember('lms', "lms_courses_{$studentId}", $this->url('lms', "courses/{$studentId}"));

        // Never render a non-http(s) link (e.g. "javascript:...") coming from an external system
        return array_values(array_map(function (array $course) {
            $link = (string) ($course['lms_link'] ?? '');
            $course['lms_link'] = Str::startsWith($link, ['https://', 'http://']) ? $link : '#';

            return $course;
        }, array_filter($courses, 'is_array')));
    }

    /** Fetch current library loans */
    public function getLibraryLoans($studentId): array
    {
        return $this->remember('library', "library_loans_{$studentId}", $this->url('library', "loans/{$studentId}"));
    }

    /**
     * Sources that were stale/unavailable while serving this request.
     *
     * @return array<string,string>
     */
    public function degradedSources(): array
    {
        return $this->degraded;
    }

    public static function gradesCacheKey($studentId): string
    {
        return "sis_grades_{$studentId}";
    }

    /**
     * Force-refresh grades, bypassing the cache. Throws on failure so a queued job can retry (RSK-05).
     */
    public function refreshGrades($studentId): array
    {
        $response = Http::acceptJson()->timeout(10)
            ->get($this->url('sis', "grades/{$studentId}"))
            ->throw();

        $data = $this->unwrap($response->json());
        $this->store(self::gradesCacheKey($studentId), $data);

        return $data;
    }

    // ---------------------------------------------------------------------

    private function remember(string $source, string $key, string $url): array
    {
        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached;
        }

        try {
            $response = Http::acceptJson()
                ->timeout(config('services.campus.http_timeout', 2))
                ->get($url);

            if ($response->successful()) {
                $data = $this->unwrap($response->json());
                $this->store($key, $data);

                return $data;
            }

            Log::warning("Campus API [{$source}] returned HTTP {$response->status()}");
        } catch (ConnectionException $e) {
            Log::warning("Campus API [{$source}] unreachable: {$e->getMessage()}");
        }

        $stale = Cache::get($key.':stale');
        if (is_array($stale)) {
            $this->degraded[$source] = 'stale';

            return $stale;
        }

        $this->degraded[$source] = 'unavailable';

        return [];
    }

    private function store(string $key, array $data): void
    {
        Cache::put($key, $data, config('services.campus.cache_ttl', 3600));
        Cache::put($key.':stale', $data, self::STALE_TTL);
    }

    /** Accept either a bare list or a {"data": [...]} envelope. */
    private function unwrap(mixed $json): array
    {
        if (is_array($json) && isset($json['data']) && is_array($json['data'])) {
            return $json['data'];
        }

        return is_array($json) ? $json : [];
    }

    private function url(string $service, string $path): string
    {
        return rtrim((string) config("services.{$service}.endpoint"), '/').'/'.ltrim($path, '/');
    }
}
