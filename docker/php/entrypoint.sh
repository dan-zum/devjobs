#!/bin/sh
set -e

cd /var/www/html

echo "[entrypoint] Optimizando Laravel..."
php artisan config:cache
php artisan route:cache || echo "[entrypoint] route:cache omitido"
php artisan view:cache

# Los archivos de caché se generan como root; se devuelven a www-data
chown -R www-data:www-data storage bootstrap/cache

echo "[entrypoint] Iniciando: $*"
exec "$@"
