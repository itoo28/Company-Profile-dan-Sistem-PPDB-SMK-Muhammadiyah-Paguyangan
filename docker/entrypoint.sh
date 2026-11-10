#!/bin/sh
set -e

# Render assigns dynamic PORT environment variable (defaults to 80 if not set)
PORT="${PORT:-80}"
echo "==> [Entrypoint] Menyesuaikan Apache untuk mendengarkan port: ${PORT}..."
sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Pastikan direktori storage & bootstrap cache berizin benar
echo "==> [Entrypoint] Memastikan struktur direktori dan izin storage/cache..."
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Buat symlink storage publik jika belum ada
echo "==> [Entrypoint] Menghubungkan storage publik (storage:link)..."
php artisan storage:link --force || true

# Jalankan migrasi database otomatis jika RUN_MIGRATIONS bernilai true
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "==> [Entrypoint] Menjalankan database migrations (php artisan migrate --force)..."
    php artisan migrate --force || true
fi

# Optimasi cache Laravel pada saat runtime jika APP_KEY telah disetel
if [ -n "$APP_KEY" ]; then
    echo "==> [Entrypoint] Mengoptimalkan konfigurasi dan cache Laravel..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> [Entrypoint] Memulai Apache Web Server di foreground..."
exec "$@"
