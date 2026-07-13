#!/usr/bin/env bash
set -e

echo "Running Laravel database migrations..."
php artisan config:clear --no-interaction
php artisan migrate --force --no-interaction

# Emit non-secret connection details in the Render deploy log. This confirms
# that Laravel is using the intended Supabase PostgreSQL database.
php artisan db:show --database=pgsql --no-interaction

exec "$@"
