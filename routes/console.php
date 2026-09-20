<?php

use Illuminate\Support\Facades\Schedule;

// RSK-05 mitigation: keep grades in sync with the SIS, retrying automatically on failure.
// Requires the Laravel scheduler (`php artisan schedule:work` locally, or a cron entry:
//   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1 )
// and a queue worker (`php artisan queue:work`).
Schedule::command('portal:sync-grades')->everyThirtyMinutes()->withoutOverlapping();
