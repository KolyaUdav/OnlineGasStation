FROM php:8.2-fpm-alpine

# 1. Системные зависимости (исправлено имя пакета на icu-dev)
RUN apk add --no-cache \
    libcurl \
    curl-dev \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    libzip-dev

# 2. Установка стандартных расширений PHP
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    curl \
    xml \
    dom \
    pcntl \
    intl \
    opcache \
    zip

# 3. Установка PCOV и Redis через PECL
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install pcov redis \
    && docker-php-ext-enable pcov redis \
    && apk del .build-deps

# 4. Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
