#!/usr/bin/env bash
set -e

echo "Running Laravel database migrations..."
php artisan config:clear --no-interaction
# Use the direct (non-pooled) connection for migrations — see config/database.php.
php artisan migrate --force --no-interaction --database=pgsql_direct

# Emit non-secret connection details in the Render deploy log. This confirms
# that Laravel is using the intended Neon PostgreSQL database.
php artisan db:show --database=pgsql --no-interaction

exec "$@"
