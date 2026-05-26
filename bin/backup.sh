#!/usr/bin/env bash
set -euo pipefail

# backup.sh — snapshot the MySQL database and user uploads.
# Designed for cron on the Azure VM:
#   0 3 * * * /srv/nusantara/bin/backup.sh >> /var/log/nusantara-backup.log 2>&1
#
# Env (optional):
#   BACKUP_DIR     where to write backups (default: /var/backups/nusantara)
#   RETAIN_DAYS    how many days of backups to keep (default: 7)
#   AZURE_CONTAINER  if set, also uploads to Azure Blob Storage via `az` CLI

cd "$(dirname "$0")/.."

BACKUP_DIR="${BACKUP_DIR:-/var/backups/nusantara}"
RETAIN_DAYS="${RETAIN_DAYS:-7}"
TIMESTAMP="$(date -u +%Y%m%d-%H%M%SZ)"

log() { printf '[%s] %s\n' "$(date -u +%H:%M:%SZ)" "$*"; }

mkdir -p "$BACKUP_DIR"

# 1. MySQL dump — runs inside the db container with consistent snapshot.
DB_FILE="${BACKUP_DIR}/db-${TIMESTAMP}.sql.gz"
log "dumping database -> ${DB_FILE}"
docker compose exec -T db sh -c '
    mysqldump \
        --user=root \
        --password="$MYSQL_ROOT_PASSWORD" \
        --single-transaction \
        --quick \
        --routines \
        --triggers \
        --events \
        --set-gtid-purged=OFF \
        --no-tablespaces \
        "$MYSQL_DATABASE"
' | gzip -9 > "${DB_FILE}.tmp"
mv "${DB_FILE}.tmp" "${DB_FILE}"
log "  -> $(du -h "${DB_FILE}" | cut -f1)"

# 2. Uploads tar — snapshot the app_uploads volume via the app service
#    (it already mounts the volume; no need to know its compose-prefixed name).
UPLOADS_FILE="${BACKUP_DIR}/uploads-${TIMESTAMP}.tar.gz"
log "archiving uploads -> ${UPLOADS_FILE}"
docker compose run --rm \
    --volume "${BACKUP_DIR}:/backup" \
    --no-deps \
    --entrypoint sh \
    app \
    -c "tar -czf /backup/uploads-${TIMESTAMP}.tar.gz.tmp -C /var/www/storage/app/public . && mv /backup/uploads-${TIMESTAMP}.tar.gz.tmp /backup/uploads-${TIMESTAMP}.tar.gz"
log "  -> $(du -h "${UPLOADS_FILE}" | cut -f1)"

# 3. Optional: ship to Azure Blob Storage (requires `az login` already done with managed identity).
if [[ -n "${AZURE_CONTAINER:-}" ]]; then
    if ! command -v az >/dev/null; then
        log "WARNING: AZURE_CONTAINER set but 'az' CLI not installed; skipping upload"
    else
        log "uploading to Azure Blob: ${AZURE_CONTAINER}"
        az storage blob upload-batch \
            --destination "${AZURE_CONTAINER}" \
            --source "${BACKUP_DIR}" \
            --pattern "*-${TIMESTAMP}*" \
            --auth-mode login \
            --overwrite false \
            >/dev/null
        log "  -> uploaded"
    fi
fi

# 4. Rotate — delete files older than RETAIN_DAYS.
log "pruning backups older than ${RETAIN_DAYS} days"
find "$BACKUP_DIR" -maxdepth 1 -type f \( -name 'db-*.sql.gz' -o -name 'uploads-*.tar.gz' \) -mtime "+${RETAIN_DAYS}" -print -delete

log "done"
