#!/bin/bash

set -e

# If a Vite dev-server "hot" file exists, Laravel will try to load assets from it
# (e.g. http://127.0.0.1:5173). In Docker we want built assets from public/build.
rm -f public/hot

# Ensure .env exists
if [ ! -f .env ] && [ -f .env.example ]; then
  cp .env.example .env
fi

# Wait for MySQL only if we plan to touch the DB on startup
if [ "${RUN_MIGRATIONS_ON_STARTUP:-false}" = "true" ] || [ "${RUN_SEED_ON_STARTUP:-false}" = "true" ]; then
  until php artisan migrate:status > /dev/null 2>&1; do
    echo "Waiting for database..."
    sleep 2
  done
fi

# Run Laravel commands
if ! grep -qE '^APP_KEY=base64:' .env 2>/dev/null; then
  php artisan key:generate --force
fi

php artisan storage:link || true

if [ "${RUN_MIGRATIONS_ON_STARTUP:-false}" = "true" ]; then
  php artisan migrate --force
fi

if [ "${RUN_SEED_ON_STARTUP:-false}" = "true" ]; then
  php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder --force
fi

# Start the main command
exec "$@"