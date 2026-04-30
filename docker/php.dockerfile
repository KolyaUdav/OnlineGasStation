FROM php:8.2-fpm-alpine

# 1. Системные зависимости (нужны для работы расширений)
RUN apk add --no-cache \
    libcurl \
    curl-dev \
    oniguruma-dev \
    libxml2-dev

# 2. Установка стандартных расширений PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    curl \
    xml \
    dom

# 3. Установка PCOV (через временные зависимости для компиляции)
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install pcov \
    && docker-php-ext-enable pcov \
    && apk del .build-deps

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
