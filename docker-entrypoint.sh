#!/usr/bin/env sh
set -e

# Restore pre-seeded SQLite database if missing or empty (cold start takes < 2ms)
if [ ! -f /app/database/database.sqlite ] || [ ! -s /app/database/database.sqlite ]; then
    echo "Restoring database from demo_template.sqlite..."
    cp /app/database/demo_template.sqlite /app/database/database.sqlite
    chmod 664 /app/database/database.sqlite || true
fi

# Restore demo images into storage if missing (Render ephemeral storage)
echo "Ensuring public demo storage assets..."
mkdir -p /app/storage/app/public
if [ -d /app/database/seed_assets ]; then
    cp -rn /app/database/seed_assets/* /app/storage/app/public/ 2>/dev/null || cp -r /app/database/seed_assets/* /app/storage/app/public/
fi

# Ensure storage link exists
php artisan storage:link --quiet || true

# Production caches for ultra-fast response times
php artisan config:cache --quiet || true
php artisan route:cache --quiet || true
php artisan view:cache --quiet || true

# Start FrankenPHP on Render assigned port
LISTEN_PORT="${PORT:-8000}"
echo "Application ready. Starting FrankenPHP on port ${LISTEN_PORT}..."
export SERVER_NAME=":${LISTEN_PORT}"

exec frankenphp run --config /etc/caddy/Caddyfile
