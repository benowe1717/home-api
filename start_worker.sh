#!/bin/bash

web_dir="/var/www/html"
console="$web_dir/bin/console"

thread="$1"
time_limit="$2"

if [ -z "$thread" ]; then
    echo "Please specify a thread name!"
    exit 1
fi

if [ -z "$time_limit" ]; then
    echo "Please specify a time limit!"
    exit 1
fi

while true; do
    php "$console" messenger:consume "$thread" --time-limit="$time_limit" || exit 1
done
