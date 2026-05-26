#!/bin/bash

set -e

if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.docker /var/www/.env
fi

export $(grep -v '^#' /var/www/.env | grep -v '^$' | xargs)

echo "==> Menunggu database siap... (host: ${DB_HOST})"
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "  -> Database belum siap, tunggu 2 detik..."
    sleep 2
done
echo "  -> Database siap!"

echo "==> Menunggu Redis siap... (host: ${REDIS_HOST})"
until (echo > /dev/tcp/${REDIS_HOST}/${REDIS_PORT:-6379}) 2>/dev/null; do
    echo "  -> Redis belum siap, tunggu 2 detik..."
    sleep 2
done
echo "  -> Redis siap!"

exec "$@"
