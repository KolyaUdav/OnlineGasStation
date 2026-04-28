FROM php:8.2-fpm-alpine

# Устанавливаем системные зависимости для расширений
RUN apk add --no-cache \
    libcurl \
    curl-dev \
    oniguruma-dev \
    libxml2-dev

# Устанавливаем и включаем расширения PHP
# Большинство из твоего списка уже есть в ядре, добавляем недостающие:
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    curl \
    xml \
    dom

# Ставим Composer (он нам точно пригодится)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
