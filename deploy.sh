#!/bin/bash

web_dir="/var/www/html"
console="$web_dir/bin/console"

# Install dependencies
composer install --no-dev --optimize-autoloader || exit 1

# Dump environment variables
composer dump-env "$APP_ENV" || exit 1

# Run database migrations
php "$console" doctrine:migrations:migrate --no-interaction || exit 1

# Clear cache
php "$console" cache:clear || exit 1

# Start up workers to handle background tasks
workers=("scheduler_expire-accesstokens")
for worker in "${workers[@]}"; do
    nohup ./start_worker.sh "$worker" "$TIME_LIMIT" &
done
