# Use PHP 8.4 CLI as base (Supports Filament v3 requirement)
FROM php:8.4-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    dos2unix \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Install PHP dependencies (Optimize for production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Fix permissions
RUN mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Remove any local cache files that might have been copied
RUN rm -f bootstrap/cache/*.php

# Expose port (Documentation only, Railway overrides this)
EXPOSE 8080

# Environment variables to force paths
ENV VIEW_COMPILED_PATH=/tmp/views

# Start command: Run directly to avoid script issues
# We use the built-in PHP server pointing to the public directory
CMD sh -c "mkdir -p /tmp/views storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache && chmod -R 777 storage bootstrap/cache && php artisan config:clear && php artisan view:clear && php -S 0.0.0.0:${PORT:-8080} -t public/"
