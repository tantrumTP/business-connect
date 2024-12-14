#!/bin/bash

# Exit immediately if a command exits with a non-zero status
set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Creating storage symlink..."
php artisan storage:link

echo "Starting NGINX and PHP-FPM..."
/start.sh
