# Stage 1: Base Image with PHP-FPM and required extensions
FROM php:8.3-fpm AS base

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

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# --- Build Step 1: PHP Dependencies ---
COPY composer.json composer.lock ./
# Install dependencies but respect platform requirements (or ignore if strictly needed, but better to fix env)
# Using --no-scripts so we don't run post-install scripts that might need full code
RUN composer install --no-dev --optimize-autoloader --no-scripts

# --- Build Step 2: JS Dependencies & Build ---
COPY package.json ./
# Copy lock file if exists, otherwise npm install will generate one
COPY package-lock.json* ./
RUN npm install

# --- Build Step 3: Copy App & Build Assets ---
COPY . .

# Build frontend assets
RUN npm run build

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
