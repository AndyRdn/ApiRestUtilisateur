FROM php:8.2-fpm-alpine

RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-dev


RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(getconf _NPROCESSORS_ONLN) gd pdo pdo_pgsql zip


COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

COPY docker/entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

EXPOSE 9000

USER www-data

ENTRYPOINT ["/docker-entrypoint.sh"]

CMD ["php-fpm"]