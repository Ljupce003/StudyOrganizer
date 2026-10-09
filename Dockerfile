# syntax=docker/dockerfile:1
FROM php:8.4-apache-bookworm AS php-base

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates curl gosu libfreetype6-dev libicu-dev libjpeg62-turbo-dev \
        libonig-dev libpng-dev libpq-dev libzip-dev unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd intl mbstring opcache pcntl pdo_pgsql zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

FROM php-base AS dependencies
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress
COPY . .
# Laravel / Filament package discovery must not need a production DB or secrets.
RUN mkdir -p storage/app/public storage/app/private storage/app/materials storage/app/purifier \
        storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --classmap-authoritative --no-scripts \
    && CACHE_STORE=array SESSION_DRIVER=array LOG_CHANNEL=stderr php artisan package:discover --ansi \
    && CACHE_STORE=array SESSION_DRIVER=array LOG_CHANNEL=stderr php artisan filament:assets \
    && composer check-platform-reqs --no-dev

FROM node:24-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js tailwind.config.js postcss.config.js ./
# Tailwind scans Laravel's pagination templates as well as the application's views.
COPY --from=dependencies /var/www/html/vendor ./vendor
RUN npm run build

FROM php-base AS runtime
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr
COPY --from=dependencies /var/www/html /var/www/html
COPY --from=assets /app/public/build ./public/build
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-production.ini
COPY docker/entrypoint.sh /usr/local/bin/studyorganizer-entrypoint
RUN ln -s /var/www/html/storage/app/public public/storage \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x /usr/local/bin/studyorganizer-entrypoint

EXPOSE 80
ENTRYPOINT ["studyorganizer-entrypoint"]
CMD ["apache2-foreground"]
