#!/bin/bash
# Matikan exit on error agar container tetap hidup untuk debugging
set +e 

echo "--- STARTING DEPLOYMENT SCRIPT (DEBUG MODE) ---"

# 1. Setup Storage Permissions
echo "[INFO] Fixing storage permissions..."
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# 2. Dynamic Port for Railway
if [ ! -z "$PORT" ]; then
    echo "[INFO] Updating Nginx port to $PORT..."
    sed -i "s/listen 8080;/listen $PORT;/g" /etc/nginx/sites-available/default
fi

# 3. DB Configuration Parsing
if [ -n "$DATABASE_URL" ] || [ -n "$MYSQL_URL" ]; then
    echo "[INFO] Detected Connection URL. Parsing..."
    target_url="${DATABASE_URL:-$MYSQL_URL}"
    # Use PHP to parse URL safely, suppress errors
    eval $(php -r "
        try {
            \$url = parse_url('$target_url');
            if (\$url) {
                echo 'export DB_CONNECTION=' . ((\$url['scheme'] ?? '') == 'postgres' ? 'pgsql' : 'mysql') . PHP_EOL;
                echo 'export DB_HOST=' . (\$url['host'] ?? '') . PHP_EOL;
                echo 'export DB_PORT=' . (\$url['port'] ?? '') . PHP_EOL;
                echo 'export DB_DATABASE=' . ltrim(\$url['path'] ?? '', '/') . PHP_EOL;
                echo 'export DB_USERNAME=' . (\$url['user'] ?? '') . PHP_EOL;
                echo 'export DB_PASSWORD=' . (\$url['pass'] ?? '') . PHP_EOL;
            }
        } catch (Exception \$e) { echo '# Parse error'; }
    ")
fi

# 4. APP_KEY Check
if [ -z "$APP_KEY" ]; then
    echo "[INFO] APP_KEY is missing. Generating fallback key..."
    cp .env.example .env
    php artisan key:generate --force
fi

# 5. Database Migration (Removed as requested)
echo "[INFO] Skipping Database Migration (Already done manually)."

# 6. Clear Caches
echo "[INFO] Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 7. Start Supervisor (Final Step)
echo "[INFO] Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
