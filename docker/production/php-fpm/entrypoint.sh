#!/bin/bash
set -e

# Initialize directories if they don't exist
if [ ! -d "/var/www/storage" ]; then
    echo "Initializing storage directory..."
    cp -r /var/www/storage-init/* /var/www/storage/ 2>/dev/null || echo "Storage init failed or already exists"
fi

# Set proper permissions for Laravel
echo "Setting permissions..."
# Ensure resources/lang exists and is writable for runtime JSON translations
mkdir -p /var/www/resources/lang 2>/dev/null || true
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/resources/lang 2>/dev/null || true
chmod -R 2775 /var/www/resources/lang 2>/dev/null || true
find /var/www/resources/lang -type f -exec chmod 664 {} \; 2>/dev/null || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# Production optimizations
echo "Running production optimizations..."
cd /var/www

# Cache configuration and routes for better performance
php artisan config:cache
# Try caching routes; if it fails, log and continue without route cache
php artisan route:cache || { echo "WARNING: route:cache failed. Skipping route cache and continuing..."; php artisan route:clear || true; }
php artisan view:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

echo "Production setup complete!"

# Execute the main container command
exec "$@"