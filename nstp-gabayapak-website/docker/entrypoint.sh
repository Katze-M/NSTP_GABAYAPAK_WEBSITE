#!/usr/bin/env bash
set -e

echo "Running Laravel database migrations..."
php artisan config:clear --no-interaction
php artisan migrate --force --no-interaction

exec "$@"
