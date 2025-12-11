FROM amazonlinux:2023

COPY . /srv/ticket-sales.com

# NGINX
RUN yum -y install yum-utils
COPY docker/web/nginx/nginx.repo /etc/yum.repos.d/nginx.repo
RUN yum -y install nginx-1.24.0
COPY docker/web/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
RUN ln -s /dev/stdout /var/log/nginx/access.log
RUN ln -s /dev/stderr /var/log/nginx/error.log

# php-fpm
RUN yum -y install php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-bcmath
RUN mkdir /run/php-fpm
RUN mkdir /var/run/php
COPY docker/web/php/php-fpm.d/zzz-php-fpm.conf /etc/php-fpm.d/zzz-php-fpm.conf
COPY docker/web/php/php-fpm.d/zzz-www.conf /etc/php-fpm.d/zzz-www.conf
RUN sed -i "s/error_log/;error_log/" /etc/php-fpm.conf
RUN sed -i "s/php_admin_value\[error_log\]/;php_admin_value\[error_log\]/" /etc/php-fpm.d/www.conf

# Node.js
RUN yum -y install tar
RUN touch ~/.bashrc
COPY docker/web/install-npm.sh /tmp/install-npm.sh
RUN chmod 755 /tmp/install-npm.sh
RUN /tmp/install-npm.sh 20.14.0

# Install PhpRedis
RUN yum -y install php-pear-1:1.10.13 php8.2-devel
RUN pecl install redis-6.1.0
COPY docker/web/php/conf.d/50-valkey.ini /etc/php.d/50-valkey.ini