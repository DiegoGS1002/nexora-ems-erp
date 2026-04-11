#!/bin/sh
set -e

# Garante que o Apache (www-data) sempre tem escrita em storage e bootstrap/cache,
# independente de quem criou os arquivos no host (volume bind mount).
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Inicia o Apache em foreground
exec apache2-foreground

