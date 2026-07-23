#!/usr/bin/env bash
set -e

cd /var/www/html

mkdir -p storage/framework/cache storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:cache

php artisan reverb:start --host="${REVERB_SERVER_HOST:-0.0.0.0}" --port="${PORT:-8080}"
