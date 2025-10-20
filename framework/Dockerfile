FROM php:8.2-apache

# Fast & reliable PHP extension installer (no apt gymnastics)
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

# Install required extensions (handles all deps internally)
# - gd: image functions (with jpeg/freetype)
# - zip: needed by phpoffice/phpspreadsheet
# - pdo_pgsql/pgsql: PostgreSQL
# - mbstring,bcmath,exif,opcache: common Laravel deps
RUN install-php-extensions gd zip pdo_pgsql pgsql mbstring bcmath exif opcache

# Enable Apache rewrite
RUN a2enmod rewrite

# Serve Laravel from public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer (direct from official installer)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer -o /tmp/c.php \
 && php /tmp/c.php --install-dir=/usr/local/bin --filename=composer \
 && rm /tmp/c.php

WORKDIR /var/www/html

# Copy composer files first for layer caching
COPY framework/composer.json framework/composer.lock ./

# Install PHP deps (optimize autoload)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy application code
COPY framework/ ./

# Ensure vendor autoload is required at bootstrap (we already fixed app.php)
# Ensure writable dirs
RUN mkdir -p storage/framework/{cache,views,sessions} \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
