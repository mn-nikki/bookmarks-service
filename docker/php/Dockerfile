FROM composer:latest as composer
FROM php:7.4-fpm-alpine3.11 as php

RUN set -xe && apk update && apk upgrade

RUN set -xe \
    && apk add --no-cache \
       shadow \
       libzip-dev \
       libintl \
       icu \
       icu-dev \
       bash \
       curl \
       libmcrypt \
       libmcrypt-dev \
       libxml2-dev \
       freetype \
       freetype-dev \
       libpng \
       libpng-dev \
       libjpeg-turbo \
       libjpeg-turbo-dev \
       postgresql-dev \
       mariadb-dev \
       pcre-dev \
       git \
       g++ \
       make \
       autoconf \
       openssh \
       util-linux-dev \
       libuuid \
       gnu-libiconv \
    && docker-php-ext-install -j$(nproc) \
        zip \
        gd \
        intl \
        soap \
        sockets \
        opcache \
        pcntl \
        sockets \
        exif \
        pdo_pgsql \
        pdo_mysql \
        iconv

RUN pecl install redis && \
    docker-php-ext-enable redis && \
    pecl install uuid && \
    docker-php-ext-enable uuid && \
    pecl install pcov && \
    docker-php-ext-enable pcov

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/app
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so php

ARG UID
ARG GID
ENV TARGET_UID ${UID:-1000}
ENV TARGET_GID ${GID:-1000}

RUN usermod -u ${TARGET_UID} www-data && groupmod -g ${TARGET_UID} www-data
RUN mkdir -p /var/www/app && chown -R www-data:www-data /var/www/app

USER ${TARGET_UID}:${TARGET_GID}
