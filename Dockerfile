# syntax=docker/dockerfile:1.6

# Stage 1: build frontend assets (Node tidak dibawa ke runtime)
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# Stage 2: PHP runtime base with extensions (shared by app + worker)
FROM php:8.4-fpm AS php-base

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        curl \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
        libicu-dev \
        zip \
        unzip \
        netcat-openbsd \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j"$(nproc)" \
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

# Opcache tuning for production
RUN { \
        echo "opcache.enable=1"; \
        echo "opcache.enable_cli=0"; \
        echo "opcache.memory_consumption=192"; \
        echo "opcache.interned_strings_buffer=16"; \
        echo "opcache.max_accelerated_files=20000"; \
        echo "opcache.validate_timestamps=0"; \
        echo "opcache.jit_buffer_size=64M"; \
        echo "opcache.jit=1255"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Stage 3: app image (php-fpm + Laravel source + vendor + built assets)
FROM php-base AS app
WORKDIR /var/www

# Composer deps first for layer cache
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

# Source
COPY . .

# Built frontend
COPY --from=node-builder /app/public/build ./public/build

# Finalize autoload, ensure storage symlink target is consistent, fix ownership
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && rm -f public/storage \
    && ln -s /var/www/storage/app/public public/storage \
    && touch .env \
    && mkdir -p storage/framework/{cache,sessions,views,testing} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Entrypoints
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY docker-entrypoint-worker.sh /usr/local/bin/docker-entrypoint-worker.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh /usr/local/bin/docker-entrypoint-worker.sh

# TCP-level FPM healthcheck (no extra deps)
HEALTHCHECK --interval=15s --timeout=3s --start-period=30s --retries=3 \
    CMD nc -z 127.0.0.1 9000 || exit 1

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

# Stage 4: nginx with public assets baked in
FROM nginx:alpine AS web
RUN apk add --no-cache curl
COPY --from=app /var/www/public /var/www/public
COPY nginx.conf /etc/nginx/conf.d/default.conf

HEALTHCHECK --interval=15s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

EXPOSE 80 443
