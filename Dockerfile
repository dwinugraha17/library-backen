# Gunakan image PHP 8.2 resmi dengan Apache
FROM php:8.2-apache

# Install dependensi sistem yang diperlukan
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Aktifkan mod_rewrite Apache untuk URL rewriting Laravel
RUN a2enmod rewrite

# Ubah document root Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy semua file project
COPY . .

# Install dependensi PHP (Production)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Berikan hak akses ke folder storage dan cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 (default Apache)
EXPOSE 80

# Start Apache saat container jalan
# Kita tambahkan perintah migrasi database otomatis sebelum start server
CMD php artisan migrate --force && apache2-foreground
