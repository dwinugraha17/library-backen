# Stage 1: Base Image with PHP-FPM and required extensions
FROM php:8.4-fpm AS base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    dos2unix \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# (Node.js removed because we build assets locally)

# --- Build Step 1: PHP Dependencies ---
COPY composer.json ./
# Install dependencies, ignoring lock file to fix platform mismatch
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# (NPM Install & Build removed)

# --- Build Step 3: Copy App ---
COPY . .

# Copy configuration files
COPY _docker/nginx/site.conf /etc/nginx/sites-available/default
COPY _docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Setup Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]