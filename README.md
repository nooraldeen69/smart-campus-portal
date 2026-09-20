# Smart Campus Portal - Laravel implementation

Code for the ITPM dossier *Smart Campus Portal* (Al-Rasheed Smart University).
This is the **application layer** for a fresh Laravel 11/12 project (PHP 8.2+): copy it over a new install.
It includes everything from your snippets (routes, API gateway service, dashboard controller, dashboard view)
plus the pieces they depend on: SSO client, models, migrations, layouts, notifications, background sync,
a mock university back-end, and tests mapped to your Acceptance Tests.

## 1. Install

```bash
composer create-project laravel/laravel smart-campus-portal
cd smart-campus-portal

# copy this folder's contents over the new project (overwrite when asked):
#   app/  bootstrap/app.php  config/services.php  database/migrations/  resources/views/
#   routes/  tests/  mock-server/
# then add the settings from .env.additions to your .env (see the note at the top of that file)

php artisan migrate
```

## 2. Run locally (3 terminals)

```bash
# 1) Mock university back-end (IdP + SIS + LMS + Library) - plain PHP, port 8001
php -S 127.0.0.1:8001 mock-server/router.php

# 2) The portal
php artisan serve                      # open http://localhost:8000  (use "localhost", not 127.0.0.1)

# 3) Optional: background grade sync + notifications
php artisan queue:work
php artisan schedule:work
```

Sign in as **Layla Hassan (20260001)** or **Omar Khalid (20260002)** on the mock SSO page.
Slow-SIS demo: `MOCK_SIS_DELAY_MS=2500 php -S 127.0.0.1:8001 mock-server/router.php` -> the dashboard
still loads (2 s timeout) and shows a "temporarily unavailable" notice.

Other commands:
```bash
php artisan portal:announce "Registration opens" "Monday 9am"   # in-portal notification to all users
php artisan portal:sync-grades                                   # queue a grade refresh for all users
php artisan test                                                 # run the test suite
```

## 3. Dossier -> code traceability

| Dossier item | Where it lives |
|---|---|
| OBJ-1 / REQ-01 / AT-01 / QM05 - SSO via University ID (WBS 1.2.1) | `Services/SsoClient.php`, `Http/Controllers/Auth/SSOController.php`, `tests/Feature/SsoLoginTest.php` |
| REQ-02 / AT-02 - grades from SIS (WBS 1.3.2) | `Services/CampusApiService.php`, `DashboardController.php`, `DashboardTest.php` |
| OBJ-2 / REQ-03 / QM01 / AT-03 - load < 2 s | cache in `CampusApiService`, 2 s HTTP timeout, `Middleware/ServerTiming.php` (`Server-Timing` header + slow-request log) |
| OBJ-4 / QM03 / AT-05 - uptime | `/up` health endpoint (point Uptime Kuma at it), stale-data fallback in `CampusApiService` |
| QM04 / AC03 / AT-06 - mobile-first | `resources/views/layouts/app.blade.php` (viewport meta), Bootstrap grid in `portal/dashboard.blade.php` |
| QM02 / AC04 / AT-04 / RSK02 - security | PKCE + `state` check, session regeneration, POST-only logout, `Middleware/SecurityHeaders.php` (HTTPS redirect in production, HSTS, nosniff, frame-deny), login throttling, encrypted sessions, `SecurityTest.php` |
| RSK01 - undocumented legacy API | isolated in `CampusApiService`; failures handled, never cached; mock server for PoC work |
| RSK05 - delayed grade sync | `Jobs/SyncStudentGradesJob.php` (5 tries, back-off 30s -> 5min), `Console/Commands/SyncGradesCommand.php`, scheduled in `routes/console.php` |
| In-scope "Notifications" | `Notifications/AnnouncementNotification.php`, `AnnounceCommand.php`, dashboard card |
| OBJ-3 - 60% fewer password-reset tickets | outcome of SSO; measured from helpdesk logs (AC05), not by code |

## 4. Changes from the snippets you pasted (and why)

1. **Logout is `POST` + CSRF** instead of a GET link - a GET logout can be triggered by any page/image on the web.
2. **Failures are no longer cached.** `Cache::remember` would store `[]` for an hour when the SIS timed out. Now only successful responses are cached; a 24 h "stale" copy is served if a back-end is down, and the dashboard shows a warning.
3. **HTTP timeouts don't crash the page** (`ConnectionException` is caught).
4. **Library module added** - OBJ-1 / QM05 name three systems (SIS, LMS, Library) but the snippets only covered two.
5. **LMS links are checked** - only `http(s)` links are rendered (blocks `javascript:` URLs from an external system).
6. **Controller comment said "concurrently"** but the calls were sequential; the comment is dropped. With caching this is fine for the target load; `Http::pool()` is the upgrade if needed.
7. `/` uses `Route::view` so `php artisan route:cache` works in production.

## 5. Not included / to decide

- **ID-token verification**: the portal calls the IdP's `userinfo` endpoint over TLS instead of validating a JWT signature. If your university IdP is OIDC, consider validating the ID token (JWKS) too. The claim names (`university_id`, `sub`, `name`, `email`) are assumptions - adjust in `SsoClient::fetchProfile()`.
- **Roles**: the dashboard is student-oriented; faculty views would need a role claim + policies.
- **Bootstrap is loaded from a CDN** for simplicity - self-host or add SRI hashes for production.
- **The `mock-server/` is for demos only** - never deploy it.
- I could not run PHP/Composer in my sandbox: every PHP file was syntax-checked with a parser, but the tests and app have **not** been executed against a live Laravel install. Run `php artisan test` first and send me any failures.
