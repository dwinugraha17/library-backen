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
        echo "Detected Connection URL. Parsing..."
        
        # Use DATABASE_URL or MYSQL_URL
        target_url="${DATABASE_URL:-$MYSQL_URL}"
        
        # Parse URL using basic string manipulation (assuming standard format: scheme://user:pass@host:port/path)
        # Remove scheme (mysql:// or postgres://)
        proto="$(echo $target_url | grep :// | sed -e's,^\(.*://\).*,\1,g')"
        url="${target_url#$proto}"
        
        # Extract User and Password
        userpass="$(echo $url | grep @ | cut -d@ -f1)"
        export DB_USERNAME="$(echo $userpass | grep : | cut -d: -f1 | tr -d '[:space:]')"
        export DB_PASSWORD="$(echo $userpass | grep : | cut -d: -f2 | tr -d '[:space:]')"
        
        # Extract Host and Port
        hostport="$(echo $url | sed -e s,$userpass@,,g | cut -d/ -f1)"
        export DB_HOST="$(echo $hostport | grep : | cut -d: -f1 | tr -d '[:space:]')"
        export DB_PORT="$(echo $hostport | grep : | cut -d: -f2 | tr -d '[:space:]')"
        
        # Extract Database Name (remove query params if any)
        dbname="$(echo $url | grep / | cut -d/ -f2- | cut -d? -f1 | tr -d '[:space:]')"
        export DB_DATABASE="$dbname"
        
        # Set Connection Type
        if [[ "$target_url" == *"postgres"* ]]; then
             export DB_CONNECTION=pgsql
        else
             export DB_CONNECTION=mysql
        fi
        
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
echo "Waiting for database connection stability (5s)..."
sleep 5

echo "Running database migrations..."
php artisan migrate --force || echo "WARNING: Database migration failed. Check your DB credentials."

echo "Publishing assets..."
php artisan storage:link || true
php artisan config:cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan view:cache

echo "Entrypoint script finished. Supervisor will now start Nginx and PHP-FPM."