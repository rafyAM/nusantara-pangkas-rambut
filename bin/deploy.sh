#!/usr/bin/env bash
set -euo pipefail

# deploy.sh — pull, build, and roll the stack on the Azure VM.
# Idempotent: safe to re-run.
#
# Usage:
#   bin/deploy.sh             # deploy current branch (HEAD)
#   bin/deploy.sh --no-pull   # skip git pull (e.g., already at desired commit)

cd "$(dirname "$0")/.."

NO_PULL=0
for arg in "$@"; do
    case "$arg" in
        --no-pull) NO_PULL=1 ;;
        -h|--help)
            sed -n '3,12p' "$0"
            exit 0
            ;;
        *)
            echo "unknown arg: $arg" >&2
            exit 2
            ;;
    esac
done

log() { printf '[%s] %s\n' "$(date -u +%H:%M:%SZ)" "$*"; }

if [[ ! -f .env ]]; then
    echo "fatal: .env not found. Copy .env.deploy.example to .env and fill it in." >&2
    exit 1
fi

if [[ "$NO_PULL" -eq 0 ]]; then
    log "git pull (fast-forward only)"
    git pull --ff-only
fi

PREV_SHA=$(git rev-parse --short HEAD)
log "deploying $PREV_SHA"

log "docker compose build"
docker compose build --pull

log "docker compose up -d"
docker compose up -d --remove-orphans

# Wait until the app container reports healthy (or 'running' if no healthcheck yet).
log "waiting for app to be healthy..."
for i in $(seq 1 60); do
    status=$(docker compose ps app --format '{{.Health}}' 2>/dev/null || echo "")
    if [[ "$status" == "healthy" ]]; then
        log "app is healthy"
        break
    fi
    if [[ "$i" -eq 60 ]]; then
        log "app did not become healthy in 60 attempts"
        docker compose logs --tail 50 app
        exit 1
    fi
    sleep 2
done

log "running migrations"
docker compose exec -T app php artisan migrate --force

log "warming caches"
docker compose exec -T app php artisan optimize:clear
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache
docker compose exec -T app php artisan event:cache || true

# Restart workers so they pick up new code (queue workers cache the booted app).
log "restarting workers"
docker compose exec -T app php artisan queue:restart || true
docker compose restart queue scheduler

log "pruning dangling images"
docker image prune -f >/dev/null

log "done. deployed $PREV_SHA"
docker compose ps
