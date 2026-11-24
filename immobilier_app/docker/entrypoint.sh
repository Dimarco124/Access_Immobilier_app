#!/bin/sh
set -e

# Link storage
php artisan storage:link || true

# Run database migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration skipped or completed"

# Seed default modules and roles
echo "Seeding default modules..."
php artisan db:seed --class=Database\\Seeders\\ModulesSeeder --force || echo "Modules already seeded or skipped"

# Cache configurations for speed in production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Fix permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/build || true

# Start supervisor (runs nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

