#!/bin/sh
set -e

# Ensure required directories exist
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/views \
         /var/www/html/bootstrap/cache

# Ensure correct ownership and permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Optional: clear stale caches to avoid boot issues
if [ -f /var/www/html/artisan ]; then
  su -s /bin/sh -c "php /var/www/html/artisan config:clear || true" www-data
  su -s /bin/sh -c "php /var/www/html/artisan cache:clear || true" www-data
  su -s /bin/sh -c "php /var/www/html/artisan route:clear || true" www-data
  su -s /bin/sh -c "php /var/www/html/artisan view:clear || true" www-data
fi

# Start Apache in foreground
exec apache2-foreground
