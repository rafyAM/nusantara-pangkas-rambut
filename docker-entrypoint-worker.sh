#!/bin/bash
set -euo pipefail

echo "==> Boot: worker"

: "${DB_HOST:?DB_HOST not set}"
: "${REDIS_HOST:?REDIS_HOST not set}"

echo "==> Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "  -> db not ready, sleeping 2s..."
    sleep 2
done
echo "  -> db ready"

echo "==> Waiting for Redis at ${REDIS_HOST}:${REDIS_PORT:-6379}..."
until nc -z "${REDIS_HOST}" "${REDIS_PORT:-6379}" 2>/dev/null; do
    echo "  -> redis not ready, sleeping 2s..."
    sleep 2
done
echo "  -> redis ready"

exec "$@"
