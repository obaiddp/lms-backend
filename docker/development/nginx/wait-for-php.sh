#!/bin/sh
set -e

PHP_FPM_HOST=${PHP_FPM_HOST:-cef-php-fpm}
PHP_FPM_PORT=${PHP_FPM_PORT:-9000}

echo "Waiting for PHP-FPM at ${PHP_FPM_HOST}:${PHP_FPM_PORT}..."
until nc -z "$PHP_FPM_HOST" "$PHP_FPM_PORT"; do
  sleep 1
done

echo "PHP-FPM is ready, starting Nginx..."
exec nginx -g "daemon off;"
