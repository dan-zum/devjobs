#!/bin/sh
set -e

cd /var/www/html

# Señal de "listo": se borra al arrancar y se crea al final.
# Jenkins y Docker la usan para saber cuándo Laravel terminó de prepararse.
rm -f /tmp/app-ready

echo "[entrypoint] Optimizando Laravel..."
php artisan config:cache
php artisan route:cache || echo "[entrypoint] route:cache omitido"
php artisan view:cache

# Los archivos de caché se generan como root; se devuelven a www-data
chown -R www-data:www-data storage bootstrap/cache

touch /tmp/app-ready
echo "[entrypoint] Laravel listo. Iniciando: $*"
exec "$@"