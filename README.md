# Smart Campus Portal

Laravel 11 implementation of the ITPM dossier (integrated SIS + LMS + Library dashboard, mobile-first UI).
It runs as-is: `vendor/` is included, the database is created automatically, and default seed data is provided.

## Run it - and open it from any device on the network

**Windows:** double-click **`start-portal.bat`**
**macOS / Linux:** `./start-portal.sh`
(or, in any terminal: `php artisan portal:serve`)

It prints something like:

```
  On this computer   :  http://localhost:8000
  On the network     :  http://192.168.1.23:8000   <- share this one
```

Open the *network* address on a phone or another PC on the same Wi-Fi/LAN, click **Sign in with University ID**,
and choose **Layla Hassan (20260001)** or **Omar Khalid (20260002)**. Stop everything with `Ctrl+C`.

**If another device cannot connect** (Windows): right-click **`allow-firewall.bat` -> Run as administrator**
(opens ports 8000 and 8001 for Private/Domain networks). Also make sure your Wi-Fi is set to a *Private*
network in Windows settings. Ports used: **8000** = portal, **8001** = mock university back-end.

Requirements: PHP 8.2+ with the `curl`, `mbstring`, `openssl`, `pdo_sqlite`, `sqlite3`, `fileinfo` extensions (all on by default in XAMPP/Laragon; enable
`extension=pdo_sqlite` / `sqlite3` in `php.ini` if missing). No Composer, Node or internet is needed to run it.

> If a folder path with Arabic characters ever causes odd errors, copy the project to a plain path such as `C:\smart-campus-portal`.

## What `portal:serve` does

1. creates/migrates the SQLite database,
2. starts the mock university back-end on port 8001 (`mock-server/router.php`),
3. starts a queue worker + scheduler (grade sync every 30 min with automatic retry, notifications),
4. serves the portal on `0.0.0.0:8000` (all network interfaces).

Options: `--port=8080`, `--mock-port=9001`, `--no-mock` (use a real IdP/SIS/LMS/Library configured in `.env`), `--no-worker`.

Logs: `storage/logs/laravel.log`, `mock-server.log`, `queue-worker.log`, `scheduler.log`.

## Other commands

```bash
php artisan portal:announce "Registration opens" "Monday 9am"   # notification to all users
php artisan portal:sync-grades                                   # queue a grade refresh now
php vendor/bin/phpunit                                           # 26 tests (no Mockery/Faker needed)
MOCK_SIS_DELAY_MS=3000 php artisan portal:serve                  # slow legacy SIS demo (Windows cmd: set MOCK_SIS_DELAY_MS=3000 first)
```

## How network access works (no IP addresses to configure)

* Links and the SSO callback URL are built from the address each visitor typed, so `localhost` and `192.168.x.x` both work at the same time.
* `.env` uses `{host}` in `SSO_AUTHORIZE_URL` / `SSO_LOGOUT_URL`, replaced by that same address, so the phone is sent to the login page on the right machine. Server-to-server calls (token, SIS, LMS, Library) stay on `127.0.0.1`.
* Bootstrap CSS/icons are self-hosted in `public/vendor`, so the pages look right on a network with no internet.
* Plain `http` is allowed on the LAN (`FORCE_HTTPS=false`). Set `FORCE_HTTPS=true` when you deploy behind HTTPS.

## Using a real university login instead of the mock

Set in `.env`: `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, `SSO_AUTHORIZE_URL`, `SSO_TOKEN_URL`, `SSO_USERINFO_URL`, optional `SSO_LOGOUT_URL`,
and `SSO_REDIRECT_URI` (the fixed callback URL registered with the IdP). Point `SIS_API_ENDPOINT`, `LMS_API_ENDPOINT`, `LIBRARY_API_ENDPOINT`
at the real systems, then run `php artisan portal:serve --no-mock`. Claim names (`university_id`/`sub`, `name`, `email`) are read in `app/Services/SsoClient.php`.

## Dossier -> code

| Dossier item | Where |
|---|---|
| OBJ-1 / REQ-01 / AT-01 - SSO | `app/Services/SsoClient.php`, `Http/Controllers/Auth/SSOController.php` |
| REQ-02 / AT-02 - grades from SIS | `app/Services/CampusApiService.php`, `DashboardController.php` |
| OBJ-2 / REQ-03 / AT-03 - load < 2 s | caching + 2 s timeout in `CampusApiService`, `Middleware/ServerTiming.php` (`Server-Timing` header) |
| OBJ-4 / AT-05 - uptime | `/up` health endpoint, stale-data fallback |
| AC03 / AT-06 - mobile-first | `resources/views/layouts/app.blade.php`, `portal/dashboard.blade.php` |
| RSK-02 / AT-04 - security | PKCE + state, POST-only logout, `Middleware/SecurityHeaders.php`, encrypted sessions |
| RSK-05 - delayed grade sync | `Jobs/SyncStudentGradesJob.php` (5 tries, back-off), scheduled in `routes/console.php` |
| Notifications | `Notifications/AnnouncementNotification.php`, `portal:announce` |

## What was fixed compared with the previous version

* Added the pieces the project skeleton was missing: `storage/` sub-folders (the first errors in your log were exactly this), the `jobs` table migration
  (the grade-sync queue crashed without it), `phpunit.xml`, `tests/TestCase.php`, `config/*.php`, `.env.example`, `.gitignore`.
* `artisan` started with a hidden BOM character; `public/index.php` replaced with the standard Laravel 11 one.
* `.env` was hard-wired to `localhost` / `127.0.0.1`, so nothing but the same PC could log in. Now network-aware (see above).
* The tests could not run (they needed Faker and Mockery, which are not in `vendor/`). They no longer do.
* The mock login page and the layout no longer load anything from a CDN.
* `portal:serve`, `start-portal.bat`, `start-portal.sh`, `allow-firewall.bat` added for one-step start-up.

## Hosting on the internet

See **[DEPLOY.md](DEPLOY.md)** - one-click deploy to Render (free) using the included `Dockerfile` + `render.yaml`, or a temporary tunnel from your PC.
