#!/bin/bash

set -euxo pipefail

PROJECT_PATH=/srv/ticket-sales.com

# Nginxユーザーに以下のファイルにアクセスする権限を与える
chmod 777 "$PROJECT_PATH/storage/logs"
chmod 777 "$PROJECT_PATH/storage/framework/views"
chmod 777 "$PROJECT_PATH/database"
chmod 777 "$PROJECT_PATH/database/database.sqlite"

# php-fpmとNGINX起動
# nginxは「-g "daemon off;"」でフォアグラウンド実行になり、コンテナが自動的に終了しなくなる
php-fpm
nginx -g "daemon off;"