FROM php:8.3-fpm-alpine

WORKDIR /www

RUN docker-php-ext-install pdo

# Set directory permissions
RUN mkdir -p var && \
    chown -R www-data:www-data var && \
    chmod -R 775 var

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

COPY ../../composer.* ./

RUN composer install --prefer-dist --no-scripts --no-progress --no-interaction

COPY ../../app ./app
COPY ../../src ./src
COPY ../../tests ./tests
COPY ../../.env.example ./.env
COPY ../../.php-cs-fixer.dist.php ./.php-cs-fixer.php

RUN composer dump-autoload --optimize
