FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
        bash \
        curl \
        freetype \
        git \
        icu-data-full \
        icu-libs \
        libjpeg-turbo \
        libpng \
        libsodium \
        libxml2 \
        libzip \
        oniguruma \
        unzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        exif \
        gd \
        intl \
        mysqli \
        pcntl \
        soap \
        zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY moodle.ini /usr/local/etc/php/conf.d/moodle.ini

WORKDIR /var/www/html
