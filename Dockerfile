# Gunakan image PHP 8.4 resmi dengan Apache
FROM php:8.4-apache

# Install dependensi sistem yang diperlukan
RUN apt-get update && apt-get install -y \
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Ubah document root ke public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Install Node.js untuk build assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Setup env sementara untuk build
RUN cp .env.example .env

# Install PHP Dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install JS Dependencies & Build
RUN npm install && npm run build

# Fix Permissions & Create Storage folders
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache

# Expose port
EXPOSE 80

# CMD: Bersihkan MPM yang bentrok di runtime, lalu start
# Kita menghapus mpm_event dan mpm_worker secara paksa sebelum start
CMD sh -c "rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf && php artisan migrate --force && apache2-foreground"
