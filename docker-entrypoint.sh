#!/bin/sh
set -e

# In dev, bind mounts can reset permissions; fix them at container start.
if [ -d /var/www/html/storage ] && [ -d /var/www/html/bootstrap/cache ]; then
  chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
  chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

# Build frontend assets if manifest is missing (happens after git clone without build)
if [ ! -f /var/www/html/public/build/manifest.json ]; then
  echo "[entrypoint] Building frontend assets (npm run build)..."
  cd /var/www/html && npm ci --silent && npm run build --silent || true
fi

exec apache2-foreground "$@"

