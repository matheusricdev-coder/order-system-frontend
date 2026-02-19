#!/bin/sh
set -e

echo "▶ Running migrations..."
php artisan migrate --force

echo "▶ Caching config & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "▶ Linking storage..."
php artisan storage:link || true

echo "▶ Starting PHP-FPM..."
php-fpm -D

echo "▶ Starting Nginx..."
exec nginx -g "daemon off;"
