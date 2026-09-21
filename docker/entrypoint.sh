#!/bin/sh
# Start-up script for the hosted (Docker) version of the Smart Campus Portal.
set -e
cd "${APP_DIR:-/app}"

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database
[ -f database/database.sqlite ] || touch database/database.sqlite

# No APP_KEY provided by the platform? Generate one for this container (sessions reset on restart).
if [ -z "$APP_KEY" ]; then
  export APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
  echo "[entrypoint] APP_KEY was not set - generated a temporary one."
fi

php artisan portal:bootstrap --no-interaction

# Cache config/routes/views for speed (env vars are read at this moment)
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction

PORT="${PORT:-8000}"
echo "[entrypoint] Serving on 0.0.0.0:${PORT}"

# PHP's built-in server with several workers (Linux). Uses Laravel's own router script so static files work.
export PHP_CLI_SERVER_WORKERS="${PHP_CLI_SERVER_WORKERS:-4}"
# (server.php expects the working directory to be public/, exactly like `php artisan serve`)
cd public
exec php -S "0.0.0.0:${PORT}" -t . ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
