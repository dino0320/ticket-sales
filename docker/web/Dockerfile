FROM amazonlinux:2023

# NGINX
RUN yum -y install yum-utils
COPY nginx/nginx.repo /etc/yum.repos.d/nginx.repo
RUN yum -y install nginx-1.24.0
COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
RUN ln -s /dev/stdout /var/log/nginx/access.log
RUN ln -s /dev/stderr /var/log/nginx/error.log

# php-fpm
RUN yum -y install php8.2-fpm php8.2-mysqlnd php8.2-mbstring
RUN mkdir /run/php-fpm
RUN mkdir /var/run/php
COPY php/php-fpm.d/zzz-php-fpm.conf /etc/php-fpm.d/zzz-php-fpm.conf
COPY php/php-fpm.d/zzz-www.conf /etc/php-fpm.d/zzz-www.conf
RUN sed -i "s/error_log/;error_log/" /etc/php-fpm.conf
RUN sed -i "s/php_admin_value\[error_log\]/;php_admin_value\[error_log\]/" /etc/php-fpm.d/www.conf

# Xdebug
RUN yum -y install php-pear php-devel

# Node.js
RUN touch ~/.bashrc
COPY install-npm.sh /tmp/install-npm.sh
RUN /tmp/install-npm.sh 20.14.0