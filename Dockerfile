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

# Create necessary directories and fix permissions
RUN mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Remove any local cache files that might have been copied
RUN rm -f bootstrap/cache/*.php

# Expose port (Documentation only, Railway overrides this)
EXPOSE 8080

# Start command: Run directly to avoid script issues
CMD sh -c "php artisan config:clear && php artisan cache:clear && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"
