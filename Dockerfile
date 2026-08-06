# syntax=docker/dockerfile:1

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
COPY app ./app
COPY database ./database
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

FROM node:22-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM dunglas/frankenphp:php8.4-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
        ctype curl dom fileinfo filter hash mbstring openssl pcre pdo session tokenizer xml pdo_mysql

WORKDIR /app

COPY . .
COPY --from=vendor /app/vendor ./vendor
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

COPY docker/web/Caddyfile /etc/frankenphp/Caddyfile
COPY docker/web/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]
