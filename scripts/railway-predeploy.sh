#!/usr/bin/env sh
set -eu

php artisan optimize:clear
php artisan migrate --force
