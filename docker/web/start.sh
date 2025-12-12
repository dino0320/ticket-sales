#!/bin/bash

set -euxo pipefail

PROJECT_PATH=/srv/ticket-sales.com

cd $PROJECT_PATH

php artisan env:decrypt --force --env=$APP_ENV
cp .env.$APP_ENV .env

php artisan migrate:fresh --force

# Install Xdebug
if [ "$APP_ENV" = "local" ]; then
  pecl install xdebug-3.3.1
  cp docker/web/php/conf.d/99-xdebug.ini /etc/php.d/99-xdebug.ini
fi

# nvm is not loaded so load it
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

node -v
npm -v

#npm ci

if [ "$APP_ENV" = "production" ] || [ $IS_NPM_BUILT -eq 1 ]; then
  npm run build
fi

# Add the nginx user to the root group for permission access
usermod -aG root nginx

# Give a permission for views
chmod 775 "$PROJECT_PATH/storage/framework/views"

# Start php-fpm and NGINX
# By using -g "daemon off;", NGINX runs in the foreground, preventing the container from exiting automatically
php-fpm
nginx -g "daemon off;"