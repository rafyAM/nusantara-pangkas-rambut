#!/bin/bash
set -euo pipefail

echo "==> Boot: app (PHP-FPM)"

: "${DB_HOST:?DB_HOST not set — check env_file in docker-compose.yml}"
: "${DB_DATABASE:?DB_DATABASE not set}"
: "${DB_USERNAME:?DB_USERNAME not set}"
: "${DB_PASSWORD:?DB_PASSWORD not set}"
: "${APP_KEY:?APP_KEY not set — generate with: docker compose run --rm app php -r 'echo \"APP_KEY=base64:\".base64_encode(random_bytes(32)).PHP_EOL;'}"

echo "==> Waiting for database at ${DB_HOST}:${DB_PORT:-3306}..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    echo "  -> db not ready, sleeping 2s..."
    sleep 2
done
echo "  -> db ready"

# Ensure runtime dirs exist on the mounted volume with correct ownership
mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Migrations are intentionally NOT run here.
# Run them via the deploy script: docker compose exec app php artisan migrate --force

echo "==> Warming caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

echo "==> Starting PHP-FPM"
exec "$@"
