#!/usr/bin/env bash
# Smart Campus Portal - one command to run everything, reachable from the whole network.
cd "$(dirname "$0")" || exit 1

if ! command -v php >/dev/null 2>&1; then
  echo "PHP 8.2+ was not found. Install it first (e.g. brew install php / sudo apt install php-cli php-sqlite3 php-mbstring php-xml php-curl)."
  exit 1
fi

if [ ! -f .env ]; then
  cp .env.example .env
  php artisan key:generate --force
fi

exec php artisan portal:serve "$@"
