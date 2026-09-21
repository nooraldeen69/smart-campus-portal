<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * One command to run the whole portal so it is reachable from other devices on the network:
 *
 *     php artisan portal:serve
 *
 * It (1) prepares the database, (2) prints the URLs to open, and (3) serves the portal on 0.0.0.0:8000.
 */
class PortalServeCommand extends Command
{
    protected $signature = 'portal:serve
        {--host=0.0.0.0 : Address to listen on (0.0.0.0 = reachable from the whole network)}
        {--port=8000 : Portal port}
        {--no-worker : Do not start the background queue worker + scheduler (grade sync, notifications)}';

    protected $description = 'Start the portal so it can be opened from any device on the network';

    /** @var array<string, array{0: resource, 1: array}> child processes we started (name => [process, pipes]) */
    private array $children = [];

    public function handle(): int
    {
        $host     = (string) $this->option('host');
        $port     = $this->freePort((int) $this->option('port'));

        $this->prepareApplication();

        if (! $this->option('no-worker')) {
            $this->startWorkers();
        }

        $this->banner($host, $port);

        // Blocks until Ctrl+C. (Laravel's own dev server, bound to all interfaces.)
        return $this->call('serve', ['--host' => $host, '--port' => $port]);
    }

    private function prepareApplication(): void
    {
        $db = database_path('database.sqlite');
        if (config('database.default') === 'sqlite' && ! file_exists($db)) {
            touch($db);
        }

        if (! config('app.key')) {
            $this->call('key:generate', ['--force' => true]);
        }

        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
    }

    /** Queue worker (retrying grade sync, RSK-05) + scheduler (runs portal:sync-grades every 30 min). */
    private function startWorkers(): void
    {
        $artisan = base_path('artisan');

        $a = $this->startChild('queue-worker', [PHP_BINARY, $artisan, 'queue:work', '--sleep=3', '--tries=5'], base_path());
        $b = $this->startChild('scheduler', [PHP_BINARY, $artisan, 'schedule:work'], base_path());

        ($a && $b)
            ? $this->components->info('Background queue worker and scheduler started (grade sync, notifications).')
            : $this->components->warn('Could not start the background worker/scheduler; the portal itself still works.');
    }

    private function startChild(string $name, array $command, string $cwd, array $extraEnv = []): bool
    {
        $log = storage_path("logs/{$name}.log");
        $env = $extraEnv === [] ? null : array_merge(getenv(), $extraEnv);

        $process = @proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['file', $log, 'a'], 2 => ['file', $log, 'a']],
            $pipes,
            $cwd,
            $env,
        );

        if (! is_resource($process)) {
            return false;
        }

        $this->children[$name] = [$process, $pipes];

        static $registered = false;
        if (! $registered) {
            $registered = true;
            register_shutdown_function(function () {
                foreach ($this->children as [$proc]) {
                    if (is_resource($proc)) {
                        @proc_terminate($proc);
                    }
                }
            });
        }

        return true;
    }

    private function banner(string $host, int $port): void
    {
        $ips = $this->lanAddresses();

        $this->newLine();
        $this->line('  <fg=green;options=bold>Smart Campus Portal is starting</>');
        $this->newLine();
        $this->line("  On this computer   :  <fg=cyan>http://localhost:{$port}</>");

        if ($host === '0.0.0.0') {
            if ($ips === []) {
                $this->line('  On the network     :  (no network address found - are you connected to Wi-Fi / LAN?)');
            }
            foreach ($ips as $i => $ip) {
                $label = $i === 0 ? 'On the network     ' : '                    ';
                $this->line("  {$label}:  <fg=cyan;options=bold>http://{$ip}:{$port}</>".($i === 0 ? '   <- share this one' : ''));
            }
            $this->newLine();
            $this->line('  Phones / other PCs must be on the same Wi-Fi or LAN.');
            $this->line('  If they cannot connect, allow PHP through the firewall (Windows: run allow-firewall.bat as Administrator).');
        }

        $this->newLine();
        $this->line("  Demo logins:  layla.hassan@student.example.edu   |   omar.khalid@student.example.edu");
        $this->line("  Password: password");
        $this->line('  Stop with Ctrl+C.');
        $this->newLine();
    }

    private function lanAddresses(): array
    {
        $found   = [];
        $primary = null;

        $sock = @stream_socket_client('udp://8.8.8.8:53', $errno, $errstr, 1);
        if ($sock) {
            $name = stream_socket_get_name($sock, false);
            fclose($sock);
            $ip = $name ? explode(':', $name)[0] : '';
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && ! str_starts_with($ip, '127.') && ! str_starts_with($ip, '169.254.')) {
                $primary = $ip;
                $found[] = $ip;
            }
        }

        if (function_exists('net_get_interfaces')) {
            foreach ((array) @net_get_interfaces() as $iface) {
                foreach ($iface['unicast'] ?? [] as $u) {
                    $ip = $u['address'] ?? '';
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && $this->isUsablePrivate($ip)) {
                        $found[] = $ip;
                    }
                }
            }
        }

        if ($found === [] && ($ip = gethostbyname(gethostname())) && $this->isUsablePrivate($ip)) {
            $found[] = $ip;
        }

        $private = array_values(array_unique(array_filter($found, fn ($ip) => $this->isUsablePrivate($ip))));

        return $private !== [] ? $private : array_values(array_filter([$primary]));
    }

    private function isUsablePrivate(string $ip): bool
    {
        return ! str_starts_with($ip, '127.') && ! str_starts_with($ip, '169.254.')
            && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }

    private function isListening(int $port): bool
    {
        $c = @fsockopen('127.0.0.1', $port, $errno, $errstr, 0.3);
        if ($c) {
            fclose($c);
            return true;
        }

        return false;
    }

    private function freePort(int $start): int
    {
        for ($p = $start; $p < $start + 20; $p++) {
            if (! $this->isListening($p)) {
                return $p;
            }
        }

        return $start;
    }
}
