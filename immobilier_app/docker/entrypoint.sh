#!/bin/sh
set -e

# Link storage
php artisan storage:link || true

# Run database migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration skipped or completed"

# Cache configurations for speed in production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Start supervisor (runs nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
