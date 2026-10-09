#!/bin/sh
set -eu

cd /var/www/html

# Keys must stay stable across releases; never generate one during startup.
if [ -z "${APP_KEY:-}" ]; then
    echo "APP_KEY is required. Set a permanent Laravel key in .env.production." >&2
    exit 1
fi

mkdir -p storage/app/public storage/app/private storage/app/materials storage/app/purifier \
    storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

if [ "$(id -u)" = 0 ]; then
    # Also repairs permissions when importing existing uploads into the volume.
    chown -R www-data:www-data storage bootstrap/cache
    gosu www-data php artisan config:cache
else
    php artisan config:cache
fi

if [ "$1" = "apache2-foreground" ]; then
    # Apache's master binds port 80; request handlers run as www-data.
    exec docker-php-entrypoint "$@"
fi

# CLI / queue / scheduler processes do not need root privileges.
if [ "$(id -u)" = 0 ]; then
    exec gosu www-data "$@"
fi
exec "$@"
