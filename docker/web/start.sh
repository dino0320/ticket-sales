#!/bin/bash

set -euxo pipefail

PROJECT_PATH=/srv/ticket-sales.com

cd $PROJECT_PATH

# Install Xdebug
if [ "$APP_ENV" = "local" ]; then
  pecl install xdebug-3.3.1
  cp docker/web/php/conf.d/99-xdebug.ini /etc/php.d/99-xdebug.ini
fi

# Add permissions to certain files so that specific users, such as the Nginx user and root, can access them.
chmod 777 "$PROJECT_PATH/storage/logs"
chmod 777 "$PROJECT_PATH/storage/framework/views"
chmod 777 "$PROJECT_PATH/database"
chmod 777 "$PROJECT_PATH/database/database.sqlite"

# Start php-fpm and NGINX
# By using -g "daemon off;", NGINX runs in the foreground, preventing the container from exiting automatically
php-fpm
nginx -g "daemon off;"