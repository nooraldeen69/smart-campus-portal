# Smart Campus Portal - production image (PHP 8.3 + SQLite). Used by Render / Railway / Fly / any Docker host.
FROM php:8.3-cli-bookworm

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# 1) dependencies (cached layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

# 2) application code
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction \
 && chmod +x docker/entrypoint.sh \
 && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache database \
 && chmod -R 777 storage bootstrap/cache database

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    SESSION_DRIVER=database \
    CACHE_STORE=database \
    QUEUE_CONNECTION=sync

EXPOSE 8000
CMD ["docker/entrypoint.sh"]
