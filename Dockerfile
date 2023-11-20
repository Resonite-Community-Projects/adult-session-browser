FROM php:8.0-apache
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y libmemcached11 libmemcachedutil2 build-essential libmemcached-dev libz-dev
RUN pecl install memcached
RUN echo extension=memcached.so >> /usr/local/etc/php/conf.d/memcached.ini

COPY php/index.php index.php
EXPOSE 80