# syntax=docker/dockerfile:1
#
# Production image for SecureFood School (Moodle 5.2, public-root layout).
# Code is BAKED IN and the container filesystem is mounted read-only at runtime
# (see docker-compose.prod.yml). Build from the REPO ROOT:
#
#   docker build -f deploy/Dockerfile -t sfs-moodle:$(git rev-parse --short HEAD) .
#
# -----------------------------------------------------------------------------
# Stage 1 — build PHP extensions on a throwaway toolchain layer.
# -----------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
        bash curl freetype git icu-data-full icu-libs libjpeg-turbo \
        libpng libsodium libxml2 libzip oniguruma unzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS freetype-dev icu-dev libjpeg-turbo-dev libpng-dev \
        libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        exif gd intl mysqli opcache pcntl soap zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Production PHP settings (opcache locked, errors hidden, php version hidden).
COPY deploy/moodle.prod.ini /usr/local/etc/php/conf.d/zz-moodle.ini

# Run php-fpm as the unprivileged www-data user (uid/gid 82 in alpine images).
RUN sed -i 's/^user = .*/user = www-data/;  s/^group = .*/group = www-data/' \
        /usr/local/etc/php-fpm.d/www.conf \
    && { echo 'php_admin_flag[expose_php] = off'; \
         echo 'catch_workers_output = yes'; \
         echo 'decorate_workers_output = no'; } \
       >> /usr/local/etc/php-fpm.d/www.conf

# -----------------------------------------------------------------------------
# Stage 2 — application layer: copy the Moodle tree (respecting .dockerignore).
# -----------------------------------------------------------------------------
FROM base AS app

WORKDIR /var/www/html

# Copy the whole repo, then overlay the production config.
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data deploy/config.prod.php /var/www/html/config.php

# Strip anything that must never ship to prod (belt-and-braces vs .dockerignore).
RUN rm -rf \
        /var/www/html/.git \
        "/var/www/html/SecureFood School" \
        /var/www/html/deploy \
        /var/www/html/backups \
        /var/www/html/.github \
        /var/www/html/.idea \
        /var/www/html/tests \
        /var/www/html/composer.phar \
        /var/www/html/.env /var/www/html/.env.example \
    && find /var/www/html -type d -name node_modules -prune -exec rm -rf {} + 2>/dev/null || true

USER www-data
EXPOSE 9000
CMD ["php-fpm"]

# -----------------------------------------------------------------------------
# Stage 3 — nginx image with the same immutable code baked in (static assets +
# the file-existence checks the Moodle vhost relies on). Mounted read-only.
# -----------------------------------------------------------------------------
FROM nginx:stable-alpine AS web

COPY --from=app /var/www/html /var/www/html
COPY deploy/nginx.prod.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
