#!/bin/bash

web_dir="/var/www/html"

# Ensure utilities are installed
binaries=("chown" "composer" "nohup" "php" "php-fpm")
for binary in "${binaries[@]}"; do
    command -v "$binary" >/dev/null 2>&1 || { echo "$binary is not available!"; exit 1; }
done

# Ensure symfony is installed
console="/var/www/html/bin/console"
if [ ! -f "$console" ]; then
    echo "Symfony is not available!"
    exit 1
fi

chown -R root:root "$web_dir/" || exit 1
/bin/bash "$web_dir/deploy.sh" || exit 1
chown -R www-data:www-data "$web_dir/" || exit 1
php-fpm -F || exit 1
