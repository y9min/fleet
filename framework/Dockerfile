# Use the official PHP 8.2 Apache image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpq-dev \
    libicu-dev \
    libmagickwand-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    pkg-config \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /tmp/* \
    && rm -rf /var/tmp/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Install ImageMagick extension (with error handling)
RUN pecl install imagick && docker-php-ext-enable imagick || echo "ImageMagick installation failed, continuing..."

# Install Redis extension (with error handling)
RUN pecl install redis && docker-php-ext-enable redis || echo "Redis installation failed, continuing..."

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Configure Composer (with error handling)
RUN composer --version && \
    composer config --global process-timeout 300 && \
    composer config --global memory-limit -1 || \
    echo "Composer config failed, continuing..."

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies with fallback options
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts || \
    composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs || \
    composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs --ignore-platform-req=php

# Copy application code
COPY . .

# Create .env file if it doesn't exist
RUN if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi

# Generate application key (only if .env exists)
RUN if [ -f .env ]; then php artisan key:generate --no-interaction || true; fi

# Set proper permissions (with error handling)
RUN chown -R www-data:www-data /var/www/html && \
    ([ -d /var/www/html/storage ] && chmod -R 755 /var/www/html/storage || echo "Storage directory not found") && \
    ([ -d /var/www/html/bootstrap/cache ] && chmod -R 755 /var/www/html/bootstrap/cache || echo "Bootstrap cache directory not found")

# Set environment variables
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache (with error handling)
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf && \
    echo '<VirtualHost *:80>\n\
    DocumentRoot ${APACHE_DOCUMENT_ROOT}\n\
    <Directory ${APACHE_DOCUMENT_ROOT}>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Create necessary directories
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache

# Set final permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
