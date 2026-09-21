<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/** Run at every start-up on a host (see docker/entrypoint.sh): create the database, migrate, load demo data if empty. */
class BootstrapCommand extends Command
{
    protected $signature = 'portal:bootstrap {--no-seed : Do not load demo data into an empty database}';

    protected $description = 'Prepare the database (migrate) and seed demo data when the database is empty';

    public function handle(): int
    {
        if (config('database.default') === 'sqlite') {
            $db = config('database.connections.sqlite.database');
            if ($db && $db !== ':memory:' && ! file_exists($db)) {
                @mkdir(dirname($db), 0775, true);
                touch($db);
            }
        }

        $this->call('migrate', ['--force' => true]);

        if (! $this->option('no-seed') && ! User::query()->exists()) {
            $this->call('db:seed', ['--force' => true]);
            $this->info('Empty database: demo data loaded.');
        }

        return self::SUCCESS;
    }
}
