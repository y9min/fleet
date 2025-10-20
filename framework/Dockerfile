FROM php:8.2-apache

# System deps (bookworm names) + clean apt cache
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl unzip zip \
    libzip-dev zlib1g-dev \
    libpq-dev \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-configure zip \
 && docker-php-ext-install -j$(nproc) gd zip pdo pdo_pgsql mbstring bcmath \
 && rm -rf /var/lib/apt/lists/*

# Apache rewrite
RUN a2enmod rewrite

# Serve Laravel from public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Install Composer (no mirror)
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer -o /tmp/c.php \
 && php /tmp/c.php --install-dir=/usr/local/bin --filename=composer \
 && rm /tmp/c.php

WORKDIR /var/www/html

# Install PHP deps using cache
COPY framework/composer.json framework/composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Copy application
COPY framework/ ./

# Writable dirs
RUN mkdir -p storage/framework/{cache,views,sessions} \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
