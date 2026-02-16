FROM composer:2 AS composer_stage

FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer_stage /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Code is mounted as volume; no COPY of app code
# Run as root in dev so mounted storage/logs and bootstrap/cache are writable

EXPOSE 9000
