#!/usr/bin/env bash
set -euo pipefail

# init-cert.sh — issue the first Let's Encrypt cert and switch nginx to HTTPS.
#
# Prerequisites:
#   - Domain's A/AAAA record points at this VM's public IP.
#   - Azure NSG allows inbound 80 and 443 from the internet.
#   - Stack is already running with HTTP-only nginx: `docker compose up -d`.
#
# Usage:
#   bin/init-cert.sh <domain> <email>
#   bin/init-cert.sh nusantara.example.com admin@example.com
#
# Add --staging as a third arg the first time you test, so you don't hit
# Let's Encrypt's production rate limits if something is misconfigured.

DOMAIN="${1:-}"
EMAIL="${2:-}"
STAGING="${3:-}"

if [[ -z "$DOMAIN" || -z "$EMAIL" ]]; then
    echo "usage: $0 <domain> <email> [--staging]" >&2
    exit 2
fi

cd "$(dirname "$0")/.."

TEMPLATE="nginx-ssl.conf.template"
TARGET="nginx.conf"
BACKUP="nginx.conf.bak.$(date +%Y%m%d-%H%M%S)"

if [[ ! -f "$TEMPLATE" ]]; then
    echo "fatal: $TEMPLATE not found" >&2
    exit 1
fi

# 1. Sanity check: nginx is up and serving HTTP, ACME challenge path is reachable from inside.
echo "==> Checking nginx is running..."
if ! docker compose ps nginx --format '{{.State}}' | grep -q running; then
    echo "fatal: nginx service is not running. Run 'docker compose up -d' first." >&2
    exit 1
fi

echo "==> Verifying ACME challenge path is reachable..."
TEST_TOKEN=".acme-preflight-$$"
TEST_PATH="/var/www/certbot/.well-known/acme-challenge/${TEST_TOKEN}"
docker compose exec -T certbot sh -c "mkdir -p /var/www/certbot/.well-known/acme-challenge && echo ok > ${TEST_PATH}" \
    || { echo "fatal: cannot write to certbot webroot volume" >&2; exit 1; }
if ! curl -fsS "http://${DOMAIN}/.well-known/acme-challenge/${TEST_TOKEN}" >/dev/null; then
    docker compose exec -T certbot rm -f "${TEST_PATH}" || true
    echo "fatal: GET http://${DOMAIN}/.well-known/acme-challenge/${TEST_TOKEN} failed." >&2
    echo "  Check: (1) DNS A record points to this VM, (2) port 80 reachable from internet," >&2
    echo "  (3) nginx is serving the ACME location (see nginx.conf)." >&2
    exit 1
fi
docker compose exec -T certbot rm -f "${TEST_PATH}" || true
echo "  -> ACME path OK"

# 2. Issue the certificate.
STAGING_FLAG=""
if [[ "$STAGING" == "--staging" ]]; then
    STAGING_FLAG="--staging"
    echo "==> Using Let's Encrypt STAGING environment (test cert, not trusted by browsers)."
fi

echo "==> Requesting certificate for $DOMAIN..."
docker compose run --rm --entrypoint certbot certbot certonly \
    --webroot -w /var/www/certbot \
    -d "$DOMAIN" \
    --email "$EMAIL" \
    --agree-tos \
    --no-eff-email \
    --keep-until-expiring \
    --non-interactive \
    $STAGING_FLAG

# 3. Verify cert exists in the named volume.
echo "==> Verifying cert files..."
docker compose run --rm --entrypoint sh certbot -c "test -f /etc/letsencrypt/live/${DOMAIN}/fullchain.pem" \
    || { echo "fatal: cert not found after issuance" >&2; exit 1; }
echo "  -> cert present"

# 4. Render HTTPS nginx config from template.
echo "==> Backing up current nginx.conf -> $BACKUP"
cp "$TARGET" "$BACKUP"

echo "==> Rendering $TARGET from $TEMPLATE (DOMAIN=$DOMAIN)"
sed "s/__DOMAIN__/${DOMAIN//./\\.}/g" "$TEMPLATE" > "$TARGET"

# 5. Validate config before reload.
echo "==> Validating new nginx config..."
if ! docker compose exec -T nginx nginx -t; then
    echo "fatal: nginx -t failed. Reverting." >&2
    cp "$BACKUP" "$TARGET"
    exit 1
fi

# 6. Reload nginx.
echo "==> Reloading nginx..."
docker compose exec -T nginx nginx -s reload

# 7. Final check.
echo "==> Probing https://${DOMAIN}/healthz ..."
sleep 2
if curl -fsS "https://${DOMAIN}/healthz" >/dev/null; then
    echo "  -> OK. HTTPS is live."
else
    echo "  -> WARNING: probe failed. Cert was issued but HTTPS isn't responding." >&2
    echo "  Check: docker compose logs nginx" >&2
    exit 1
fi

cat <<EOF

================================================================
Done. ${DOMAIN} is now serving HTTPS.

Renewal runs automatically every 12h inside the certbot container.
Nginx reloads every 6h to pick up renewed certs.

To force a renewal check now:
    docker compose exec certbot certbot renew --webroot -w /var/www/certbot --force-renewal

To roll back to HTTP:
    cp ${BACKUP} nginx.conf
    docker compose exec nginx nginx -s reload
================================================================
EOF
