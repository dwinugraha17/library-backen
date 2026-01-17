#!/bin/bash
set -e

echo "Starting deployment script..."

# Auto-detect Railway Database Variables and map to Laravel DB_* vars
# This allows "zero-config" connection if using Railway's default plugins.
if [ -z "$DB_HOST" ]; then
    echo "DB_HOST not set. Attempting to auto-detect Railway database..."
    
    # Check for MySQL (Railway MySQL Plugin)
    if [ -n "$MYSQLHOST" ]; then
        echo "Detected Railway MySQL configuration (Variables)."
        export DB_CONNECTION=mysql
        export DB_HOST="$MYSQLHOST"
        export DB_PORT="$MYSQLPORT"
        export DB_DATABASE="$MYSQLDATABASE"
        export DB_USERNAME="$MYSQLUSER"
        export DB_PASSWORD="$MYSQLPASSWORD"
    
    # Check for DATABASE_URL (Generic or Railway)
    elif [ -n "$DATABASE_URL" ] || [ -n "$MYSQL_URL" ]; then
        echo "Detected Connection URL. Parsing using PHP for robustness..."
        
        target_url="${DATABASE_URL:-$MYSQL_URL}"
        
        # Use PHP to parse the URL safely
        eval $(php -r "
            \$url = parse_url('$target_url');
            echo 'export DB_CONNECTION=' . (\$url['scheme'] == 'postgres' ? 'pgsql' : 'mysql') . PHP_EOL;
            echo 'export DB_HOST=' . (\$url['host'] ?? '') . PHP_EOL;
            echo 'export DB_PORT=' . (\$url['port'] ?? '') . PHP_EOL;
            echo 'export DB_DATABASE=' . ltrim(\$url['path'] ?? '', '/') . PHP_EOL;
            echo 'export DB_USERNAME=' . (\$url['user'] ?? '') . PHP_EOL;
            echo 'export DB_PASSWORD=' . (\$url['pass'] ?? '') . PHP_EOL;
        ")
        
    # Check for PostgreSQL (Railway PostgreSQL Plugin - Variables)
    elif [ -n "$PGHOST" ]; then
        echo "Detected Railway PostgreSQL configuration."
        export DB_CONNECTION=pgsql
        export DB_HOST="$PGHOST"
        export DB_PORT="$PGPORT"
        export DB_DATABASE="$PGDATABASE"
        export DB_USERNAME="$PGUSER"
        export DB_PASSWORD="$PGPASSWORD"
    else
        echo "No standard Railway database variables found. Using defaults."
    fi
else
    echo "DB_HOST is already set. Skipping auto-detection."
fi

echo "--- Database Configuration ---"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DB_HOST: $DB_HOST"
echo "DB_PORT: $DB_PORT"
echo "------------------------------"

# Fix Permissions & Create Storage folders (Runtime Check)
echo "Fixing storage permissions..."
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Update Nginx Port based on Railway PORT variable
if [ ! -z "$PORT" ]; then
    echo "Updating Nginx port to $PORT..."
    sed -i "s/listen 8080;/listen $PORT;/g" /etc/nginx/sites-available/default
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "APP_KEY is missing. Generating..."
    cp .env.example .env
    php artisan key:generate
fi



# Create storage link
echo "Creating storage link..."
php artisan storage:link || true

# Run Migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction -v || echo "WARNING: Database migration failed. This is expected if DB is not ready yet."

echo "Caching configuration..."
php artisan storage:link || true
php artisan config:cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan view:cache

echo "Entrypoint script finished. Supervisor will now start Nginx and PHP-FPM."