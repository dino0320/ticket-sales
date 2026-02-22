#!/bin/bash

set -euxo pipefail

PROJECT_PATH=/srv/ticket-sales.com

cd $PROJECT_PATH

# composer install and npm ci
if [ "$APP_ENV" = "local" ]; then
  composer install
  # nvm is not loaded so load it
  source ~/.bashrc
  npm ci

if [ $IS_NPM_BUILT -eq 1 ]; then
  npm run build
fi
fi

php artisan env:decrypt --force --env=$APP_ENV
cp .env.$APP_ENV .env

php artisan migrate:fresh --force
php artisan app:create-admin-user

# Give permissions for log output etc.
chmod 775 "$PROJECT_PATH/storage/logs"
chmod 775 "$PROJECT_PATH/storage/framework/views"

# Start php-fpm and NGINX
# By using -g "daemon off;", NGINX runs in the foreground, preventing the container from exiting automatically
php-fpm
nginx -g "daemon off;"