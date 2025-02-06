ARG PHP_VERSION=8.2.4
ARG NGINX_VERSION=1.22.0

FROM alpine as preprocessor
RUN apk add --no-cache dos2unix
COPY docker/php/docker-entrypoint.sh /entrypoint.sh
RUN dos2unix /entrypoint.sh && chmod +x /entrypoint.sh

FROM php:${PHP_VERSION}-fpm-alpine AS app_php

ARG WORKDIR=/app

RUN docker-php-source extract

RUN apk add --update --virtual .build-deps autoconf g++ make pcre-dev icu-dev openssl-dev libxml2-dev git libpng-dev

RUN apk add postgresql-dev
RUN docker-php-ext-install pgsql pdo_pgsql
RUN apk del postgresql-libs libsasl db

RUN pecl install apcu
RUN docker-php-ext-enable apcu opcache

RUN apk add icu-libs icu
RUN docker-php-ext-install intl

RUN runDeps="$( \
    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
        | tr ',' '\n' \
        | sort -u \
        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
)"
RUN apk add --no-cache --virtual .app-phpexts-rundeps $runDeps

RUN pecl clear-cache
RUN docker-php-source delete
RUN apk del --purge .build-deps
RUN rm -rf /tmp/pear
RUN rm -rf /var/cache/apk/*

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini
COPY docker/php/php-cli.ini $PHP_INI_DIR/conf.d/php-cli.ini

COPY config/firebase/service-account.json ${WORKDIR}/config/firebase/service-account.json
RUN chmod 644 ${WORKDIR}/config/firebase/service-account.json

RUN mkdir -p ${WORKDIR}
WORKDIR ${WORKDIR}

ENV COMPOSER_ALLOW_SUPERUSER=1



COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
    composer install --prefer-dist --no-autoloader --no-scripts  --no-progress; \
    composer clear-cache

RUN set -eux \
    && mkdir -p var/cache var/log \
    && composer dump-autoload --classmap-authoritative

VOLUME ${WORKDIR}/var

COPY --from=preprocessor /entrypoint.sh /usr/local/bin/docker-entrypoint



ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM nginx:${NGINX_VERSION}-alpine AS app_nginx

COPY docker/nginx/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /app/public