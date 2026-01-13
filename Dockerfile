# Gunakan image PHP 8.4 resmi dengan Apache (Sesuai kebutuhan composer.lock)
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

# Aktifkan mod_rewrite Apache untuk URL rewriting Laravel
RUN a2enmod rewrite

# Fix: Pastikan hanya satu MPM yang dimuat (prefork untuk PHP module)
RUN a2dismod mpm_event && a2enmod mpm_prefork

# Ubah document root Apache ke folder public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Install Node.js (Dibutuhkan untuk build assets Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy semua file project
COPY . .

# Buat file .env dari example agar composer tidak error
RUN cp .env.example .env

# Install dependensi PHP (Production)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Install dependensi JS dan build assets
RUN npm install && npm run build

# Buat folder storage yang hilang dan berikan hak akses
RUN mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 (default Apache)
EXPOSE 80

# Start Apache saat container jalan
# Menjalankan migrasi database otomatis sebelum start server
CMD php artisan migrate --force && apache2-foreground