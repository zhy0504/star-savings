#!/bin/bash
set -e

echo "======================================"
echo "Starting Laravel initialization..."
echo "======================================"

# Create .env file if it doesn't exist
if [ ! -f "/var/www/html/.env" ]; then
    echo "Creating .env file from environment variables..."
    cat > /var/www/html/.env << EOF
APP_NAME="Star Savings"
APP_ENV=${APP_ENV:-production}
APP_KEY=
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=debug

DB_CONNECTION=${DB_CONNECTION:-sqlite}
DB_DATABASE=${DB_DATABASE:-/var/www/html/storage/app/database.sqlite}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOF
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi

# Verify frontend build exists
if [ ! -d "/var/www/html/public/dist" ]; then
    echo "⚠ WARNING: Frontend build not found at /var/www/html/public/dist"
else
    echo "✓ Frontend build found"
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force
echo "✓ Migrations completed"

# Create storage symbolic link
echo "Creating storage symbolic link..."
php artisan storage:link || echo "Storage link already exists or failed, continuing..."
echo "✓ Storage link created"

# Set proper permissions
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/public
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/public
echo "✓ Permissions set"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
    echo "✓ APP_KEY generated"
fi

# Clear and cache configuration (production optimization)
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Optimization completed"

echo "======================================"
echo "Laravel initialization completed!"
echo "Starting PHP-FPM..."
echo "======================================"

# Start PHP-FPM
exec php-fpm
