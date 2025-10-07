FROM amazonlinux:2023

RUN yum -y install php8.2 unzip-6.0

COPY install-composer.sh /tmp/install-composer.sh
RUN /tmp/install-composer.sh /usr/local/bin/composer 2.7.6