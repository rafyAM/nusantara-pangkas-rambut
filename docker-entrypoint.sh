#!/bin/bash

set -e

echo "==> Menyiapkan environment..."

if [ ! -f /var/www/.env ]; then
    echo "  -> .env tidak ditemukan, copy dari .env.docker"
    cp /var/www/.env.docker /var/www/.env
fi

# Load variabel dari .env ke shell dengan aman (mendukung nilai yang mengandung spasi)
set -a
# shellcheck source=/dev/null
source /var/www/.env
set +a

echo "==> Generate APP_KEY jika kosong..."
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --no-interaction --force
    set -a
    source /var/www/.env
    set +a
fi

echo "==> Menunggu database siap... (host: ${DB_HOST})"
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "  -> Database belum siap, tunggu 2 detik..."
    sleep 2
done
echo "  -> Database siap!"

echo "==> Menjalankan migrasi..."
php artisan migrate --force --no-interaction

echo "==> Optimasi cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Menyetel permission storage..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "==> Menjalankan PHP-FPM..."
exec php-fpm
