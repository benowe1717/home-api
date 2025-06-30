#!/bin/bash

COMPOSER=$(which composer)
NOHUP=$(which nohup)
PHP=$(which php)

BINARIES=("composer" "nohup" "php")
WORKERS=("scheduler_expire-accesstokens")

APP_ENV="prod"
TIME_LIMIT=3600

for ((i=0; i < ${#BINARIES[@]}; i++)); do
    RESULT=$(which "${BINARIES[i]}" > /dev/null 2>&1; echo $?)
    if [[ "$RESULT" != 0 ]]; then
        /bin/echo "ERROR: Unable to locate ${BINARIES[i]}!"
        exit 1
    fi
done

$COMPOSER dump-env $APP_ENV
$COMPOSER install --no-dev --optimize-autoloader
$PHP ./bin/console doctrine:migrations:migrate --no-interaction
$PHP ./bin/console cache:clear

for worker in "${WORKERS[@]}"; do
    $NOHUP ./start_worker.sh "$worker" "$TIME_LIMIT" &
done
