#!/bin/sh
set -e

USER_ID=${UID:-1000}
GROUP_ID=${GID:-1000}

echo "=== Laravel Docker Entrypoint ==="
echo "Starting PHP-FPM with UID=${USER_ID}, GID=${GROUP_ID}"

# Note: Running as root to handle permissions, using numeric UID/GID for ownership
echo "Using UID=${USER_ID}, GID=${GROUP_ID} for file ownership..."

# Comprehensive Laravel directory structure creation
echo "Creating comprehensive Laravel directory structure..."
mkdir -p /var/www/storage/app/public \
         /var/www/storage/logs \
         /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/testing \
         /var/www/storage/framework/views \
         /var/www/bootstrap/cache \
         /var/www/vendor \
         /var/www/node_modules 2>/dev/null || true

# Handle public/storage separately (might be a symlink)
if [ ! -e /var/www/public/storage ]; then
    mkdir -p /var/www/public/storage 2>/dev/null || true
fi

# Create essential Laravel files
echo "Creating essential Laravel files..."
touch /var/www/storage/logs/laravel.log 2>/dev/null || true
touch /var/www/.env 2>/dev/null || true
mkdir -p /var/www/resources/lang 2>/dev/null || true

# Fix ownership: keep host user as owner, set group to www-data so PHP-FPM can write
echo "Setting ownership of /var/www to ${USER_ID}:www-data..."
chown -R ${USER_ID}:www-data /var/www 2>/dev/null || echo "Warning: Could not change ownership (this is normal on some systems)"

echo "Setting collaborative permissions (u+rwX,g+rwX) and setgid on dirs..."
# Directories: 775 with setgid bit so new files inherit group www-data
find /var/www -type d -exec chmod 2775 {} \; 2>/dev/null || true
# Files: 664
find /var/www -type f -exec chmod 664 {} \; 2>/dev/null || true

# Ensure Laravel-specific writable directories remain writable
echo "Ensuring Laravel-specific writable directories..."
chmod -R 2775 /var/www/storage /var/www/bootstrap/cache /var/www/resources/lang 2>/dev/null || true
find /var/www/storage -type f -exec chmod 664 {} \; 2>/dev/null || true
find /var/www/resources/lang -type f -exec chmod 664 {} \; 2>/dev/null || true

# Set executable permissions for artisan and other scripts
echo "Setting executable permissions for Laravel scripts..."
[ -f /var/www/artisan ] && chmod +x /var/www/artisan 2>/dev/null || true
find /var/www -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true

# Ensure critical files have correct permissions
chmod 664 /var/www/storage/logs/laravel.log 2>/dev/null || true
[ -f /var/www/.env ] && chmod 644 /var/www/.env 2>/dev/null || true

echo "Permissions setup completed successfully!"

# Configure PHP-FPM to listen on all interfaces
echo "Configuring PHP-FPM network settings..."
PHP_FPM_CONF="/usr/local/etc/php-fpm.d/www.conf"

# Remove any existing listen directives that might conflict
sed -i '/^listen\s*=/d' "$PHP_FPM_CONF" 2>/dev/null || true
sed -i '/^listen\.allowed_clients/d' "$PHP_FPM_CONF" 2>/dev/null || true

# Add our network configuration (remove listen.allowed_clients to allow all)
echo "listen = 0.0.0.0:9000" >> "$PHP_FPM_CONF"

echo "PHP-FPM configured to listen on 0.0.0.0:9000 (all clients allowed)"

# Create a marker file to indicate setup is complete
touch /var/www/.docker-setup-complete

# Clear Laravel caches
if command -v php >/dev/null 2>&1 && [ -f artisan ]; then
  echo "Clearing Laravel caches..."
  php artisan config:clear || true
  php artisan route:clear || true
  php artisan view:clear || true
fi

# Wait for DB if environment variable is set
if [ -n "$WAIT_FOR_DB" ]; then
  echo "Waiting for database at $WAIT_FOR_DB..."
  timeout 30 sh -c 'until nc -z $(echo "$WAIT_FOR_DB" | cut -d: -f1) $(echo "$WAIT_FOR_DB" | cut -d: -f2); do sleep 1; done' || echo "Database wait timeout"
fi

# Start PHP-FPM in foreground mode
echo "Starting PHP-FPM in foreground mode..."
echo "Container is ready and healthy!"

# Validate critical directories exist
if [ ! -d "/var/www/storage/logs" ]; then
    echo "WARNING: Laravel storage/logs directory missing, attempting to create..."
    mkdir -p /var/www/storage/logs 2>/dev/null || true
fi

if [ ! -f "/var/www/storage/logs/laravel.log" ]; then
    echo "WARNING: Laravel log file missing, attempting to create..."
    touch /var/www/storage/logs/laravel.log 2>/dev/null || true
fi

echo "All checks passed. Starting PHP-FPM..."
exec php-fpm -F
