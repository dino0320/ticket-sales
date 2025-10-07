#!/bin/bash

set -euxo pipefail

EXPECTED_CHECKSUM="$(php -r 'copy("https://composer.github.io/installer.sig", "php://stdout");')"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

VERSION_OPTION=''
if [ $# == 2 ]
then
    VERSION_OPTION="--version=$2"
fi

php composer-setup.php $VERSION_OPTION
rm composer-setup.php

COMPOSER_PATH=$1
mv composer.phar $COMPOSER_PATH