# Stage 1: Base Image with PHP-FPM and required extensions
FROM php:8.4-fpm as base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies, including nginx and supervisor
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
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js for building assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copy configuration files for Nginx and Supervisor
COPY _docker/nginx/site.conf /etc/nginx/sites-available/default
COPY _docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application files
COPY . .

# Install PHP and JS dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts \
    && npm install \
    && npm run build

# Make entrypoint executable
COPY docker-entrypoint.sh /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose the port Nginx will listen on
EXPOSE 8080

# Run the entrypoint script which handles migrations, keys, etc.
# Then, Supervisord will be started by the CMD
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# Start Supervisor to manage Nginx and PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]