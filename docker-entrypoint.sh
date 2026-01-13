#!/bin/bash
set -e

echo "Starting deployment script..."

# Fix MPM conflicts (prevent apache crash on start)
rm -f /etc/apache2/mods-enabled/mpm_event.load /etc/apache2/mods-enabled/mpm_event.conf
rm -f /etc/apache2/mods-enabled/mpm_worker.load /etc/apache2/mods-enabled/mpm_worker.conf

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is missing. Generating..."
    php artisan key:generate
    # Reload env if necessary, though artisan modifies .env file usually, 
    # but in Docker .env might be ephemeral or read once.
    # ideally we want to export it for this session if artisan doesn't set it in env vars.
fi

# Configure Apache to listen on Railway's PORT
# Railway injects $PORT (e.g., 6543)
PORT=${PORT:-80}
echo "Configuring Apache to listen on port $PORT..."

# Replace strict occurrences to avoid replacing '80' in other contexts
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Run Migrations
echo "Running database migrations..."
php artisan migrate --force || echo "WARNING: Database migration failed, but starting server anyway..."

echo "Starting Apache..."
exec apache2-foreground
