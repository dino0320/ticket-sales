# --- Stage 1: Install PHP dependencies for production ---
FROM composer:2.7.6 AS composer_production
WORKDIR /app
RUN apk update && \
    apk add --no-cache libpng-dev && \
    docker-php-ext-install bcmath
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-scripts


# --- Stage 2: Install PHP dependencies (including dev) for node_production ---
FROM composer:2.7.6 AS composer_node_helper
WORKDIR /app
RUN docker-php-ext-install bcmath
COPY . .
RUN composer install


# --- Stage 3: Build frontend assets for production ---
FROM node:20.19.6-alpine AS node_production
WORKDIR /app
RUN apk update && apk add --no-cache php83 php83-mbstring php83-tokenizer && \
    ln -sf /usr/bin/php83 /usr/bin/php
COPY --from=composer_node_helper /app/vendor ./vendor
COPY . .
RUN npm ci && npm run build


# --- Stage 4: Base image with Nginx and PHP-FPM ---
FROM amazonlinux:2023 AS base

# Install Nginx, php-fpm and PhpRedis

# Install packages and remove cache
COPY docker/web/nginx/nginx.repo /etc/yum.repos.d/nginx.repo
RUN yum -y update && \
    yum -y install yum-utils nginx-1.24.0 \
    php8.2-fpm php8.2-pgsql php8.2-mbstring php8.2-bcmath \
    php-pear-1:1.10.13 php8.2-devel && \
    pecl install redis-6.1.0 && \
    yum clean all && rm -rf /var/cache/yum

# Copy configurations
COPY docker/web/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY docker/web/php/php-fpm.d/zzz-php-fpm.conf /etc/php-fpm.d/zzz-php-fpm.conf
COPY docker/web/php/php-fpm.d/zzz-www.conf /etc/php-fpm.d/zzz-www.conf
COPY docker/web/php/conf.d/50-valkey.ini /etc/php.d/50-valkey.ini

# Prepare logs and directories
RUN ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log && \
    mkdir -p /run/php-fpm /var/run/php && \
    sed -i "s/error_log/;error_log/" /etc/php-fpm.conf && \
    sed -i "s/php_admin_value\[error_log\]/;php_admin_value\[error_log\]/" /etc/php-fpm.d/www.conf

# Add the nginx user to the root group for permission access
RUN usermod -aG root nginx


# --- Stage 5: Development environment (Local) ---
FROM base AS local
ARG PROJECT_PATH=/srv/ticket-sales.com
WORKDIR $PROJECT_PATH

# Install Composer, Node.js and Xdebug

# Install packages and remove cache
RUN yum -y update && \
    yum -y install php8.2 php8.2-bcmath unzip-6.0 \
    tar && \
    pecl install xdebug-3.3.1 && \
    yum clean all && rm -rf /var/cache/yum

# Copy configurations and scripts
COPY docker/web/install-composer.sh /tmp/install-composer.sh
COPY docker/web/install-npm.sh /tmp/install-npm.sh
COPY docker/web/php/conf.d/99-xdebug.ini /etc/php.d/99-xdebug.ini

# Install Composer and Node.js
RUN chmod 755 /tmp/install-composer.sh && /tmp/install-composer.sh /usr/local/bin/composer 2.7.6 && \
    touch ~/.bashrc && chmod 755 /tmp/install-npm.sh && /tmp/install-npm.sh 20.19.6


# --- Stage 6: Production environment ---
FROM base AS production
ARG PROJECT_PATH=/srv/ticket-sales.com
WORKDIR $PROJECT_PATH
COPY . .
COPY --from=composer_production /app/vendor ./vendor
COPY --from=node_production /app/public/build ./public/build