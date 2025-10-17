# Multi-stage build for Laravel (dev, no frontend)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction
COPY . .
RUN composer install --no-dev --prefer-dist --no-interaction

FROM php:8.3-cli-alpine AS app
# Install system deps and PHP extensions
RUN apk add --no-cache git unzip icu-dev oniguruma-dev libzip-dev sqlite && \
    docker-php-ext-install pdo pdo_sqlite bcmath intl
WORKDIR /app
COPY --from=vendor /app /app
ENV APP_ENV=local \
    APP_DEBUG=true \
    LOG_CHANNEL=stderr \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/app/database/database.sqlite
EXPOSE 8000
CMD ["/bin/sh", "-lc", "php artisan migrate --force || true; php artisan serve --host=0.0.0.0 --port=8000"]
