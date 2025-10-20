#!/bin/sh
set -e

# Ensure required directories exist
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Ensure correct ownership and permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure Apache listens on Render-assigned PORT on 0.0.0.0
PORT_TO_USE=${PORT:-80}
echo "Listen 0.0.0.0:${PORT_TO_USE}" > /etc/apache2/ports.conf
if [ -f /etc/apache2/sites-available/000-default.conf ]; then
  sed -i "s#<VirtualHost \*:.*>#<VirtualHost *:${PORT_TO_USE}>#g" /etc/apache2/sites-available/000-default.conf || true
fi

# Skip artisan cache clears at boot to avoid permission noise/timeouts

# Start Apache in foreground
exec apache2-foreground
