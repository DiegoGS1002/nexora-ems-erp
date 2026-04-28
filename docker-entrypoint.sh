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

# Create storage symlink if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
  echo "[entrypoint] Creating storage symlink..."
  php /var/www/html/artisan storage:link --force 2>/dev/null || true
fi

# Clear and cache config for performance
php /var/www/html/artisan config:clear 2>/dev/null || true
php /var/www/html/artisan view:clear 2>/dev/null || true

# Start queue worker in background (needed for AI support agent)
echo "[entrypoint] Starting queue worker in background..."
su -s /bin/sh www-data -c "php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --daemon >> /var/www/html/storage/logs/queue.log 2>&1 &" || \
  php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --daemon >> /var/www/html/storage/logs/queue.log 2>&1 &

exec apache2-foreground "$@"
