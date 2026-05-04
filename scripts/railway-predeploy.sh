#!/usr/bin/env sh
set -eu

mkdir -p \
  bootstrap/cache \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs

chown -R www-data:www-data \
  bootstrap/cache \
  storage

chmod -R ug+rwX \
  bootstrap/cache \
  storage

rm -f \
  bootstrap/cache/*.php \
  storage/framework/views/*.php

php artisan view:clear
php artisan migrate --force
