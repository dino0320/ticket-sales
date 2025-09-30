FROM amazonlinux:2023

# NGINX
RUN yum -y install yum-utils
COPY nginx/nginx.repo /etc/yum.repos.d/nginx.repo
RUN yum -y install nginx-1.24.0
COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# php-fpm and php-mysqlnd
RUN yum -y install php8.2-fpm php8.2-mysqlnd php8.2-mbstring
RUN mkdir /run/php-fpm
RUN mkdir /var/run/php
COPY php/php-fpm.d/zzz-www.conf /etc/php-fpm.d/zzz-www.conf

# Xdebug
RUN yum -y install php-pear php-devel