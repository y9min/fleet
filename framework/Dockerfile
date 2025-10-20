FROM php:8.2-apache

# System deps (pgsql, zip, gd build deps)
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev libpq-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install gd zip pdo pdo_pgsql mbstring bcmath

# Enable Apache rewrite
RUN a2enmod rewrite

# Serve Laravel from public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer (no mirror)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php \
 && php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && rm /tmp/composer-setup.php

WORKDIR /var/www/html

# Copy composer files first (better caching)
COPY framework/composer.json framework/composer.lock ./

# Install PHP deps (optimize autoload)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy application code
COPY framework/ ./

# Writable dirs
RUN mkdir -p storage/framework/{cache,views,sessions} \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
