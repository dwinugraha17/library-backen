#!/bin/sh
set -e

echo "--- STARTING APP ---"
echo "PORT: ${PORT:-8080}"

# Clear caches to ensure clean boot
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear

# Link storage (ignore error if fails)
echo "Linking storage..."
php artisan storage:link || true

# Start Server
echo "Starting Artisan Serve..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
