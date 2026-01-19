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
# 1. Create necessary directories
# 2. Ensure sqlite DB exists
# 3. Generate key if missing (careful in prod, but needed if env empty)
# 4. Migrate database
# 5. Start PHP server
CMD sh -c "mkdir -p /tmp/views storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache database && chmod -R 777 storage bootstrap/cache database && touch database/database.sqlite && chmod 777 database/database.sqlite && php artisan key:generate --force --skip-if-exists && php artisan migrate --force && php artisan config:clear && php -S 0.0.0.0:${PORT:-8080} -t public/"
