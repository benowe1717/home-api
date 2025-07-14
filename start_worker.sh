#!/bin/bash

PHP=$(which php)

THREAD="$1"
TIME_LIMIT="$2"

while true; do
    $PHP ./bin/console messenger:consume "$THREAD" --time-limit="$TIME_LIMIT"
done
