# Stage 1: Build frontend assets (Node tidak dibawa ke runtime)
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: PHP runtime
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache

RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Salin hasil build assets dari stage Node
COPY --from=node-builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY docker-entrypoint-worker.sh /usr/local/bin/docker-entrypoint-worker.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint-worker.sh

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
