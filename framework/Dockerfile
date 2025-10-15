# Stage 1: Build vendor dependencies with PHP 8.2
FROM php:8.2-cli AS vendor

# Install system dependencies for PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libzip-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip intl

# Copy Composer binary and set environment
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /app

# Copy composer files and necessary Laravel directories
COPY composer.json composer.lock ./
COPY database ./database
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY public ./public
COPY resources ./resources
COPY routes ./routes
COPY storage ./storage

# Configure and install dependencies
RUN composer config platform.php 8.2.0
RUN composer install --no-dev --prefer-dist --no-progress --no-interaction --optimize-autoloader --no-scripts

# Stage 2: Runtime image using official PHP 8.2 Apache
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

# Optionally copy Composer binary (for runtime ops like dump-autoload)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Configure Composer (with error handling)
RUN composer --version || echo "Composer not required at runtime"

# Copy application code
COPY . .

# Copy pre-built vendor from composer stage
COPY --from=vendor /app/vendor /var/www/html/vendor

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
