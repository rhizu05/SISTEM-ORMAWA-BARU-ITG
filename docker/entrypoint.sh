#!/bin/sh
set -e

# Pastikan folder storage dan bootstrap cache ada
mkdir -p /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/storage/app/public/profil \
         /var/www/storage/app/proposals \
         /var/www/storage/app/lpj \
         /var/www/storage/app/surat \
         /var/www/storage/app/private \
         /var/www/bootstrap/cache

# Sesuaikan kepemilikan file untuk www-data
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Sinkronkan aset public terbaru (build CSS/JS, logo, static files) ke volume public
if [ -d /var/www/public_template ]; then
    cp -ru /var/www/public_template/. /var/www/public/ || true
fi

# Jalankan storage:link jika belum ada
if [ ! -L /var/www/public/storage ]; then
    php /var/www/artisan storage:link || true
fi

# Jika mode produksi, jalankan cache optimasi
if [ "$APP_ENV" = "production" ]; then
    echo "Running production optimizations..."
    php /var/www/artisan config:cache || true
    php /var/www/artisan route:cache || true
    php /var/www/artisan view:cache || true
    php /var/www/artisan event:cache || true
fi

exec "$@"
