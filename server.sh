#!/bin/bash
# PHP=php56
PHP=php

fpm=$PHP-fpm7.0

$fpm -p . -y var/php/php-fpm.conf
mkdir -p /tmp/nginx/client_temp
mkdir -p /tmp/nginx/cache
nginx -p . -c var/php/nginx.conf
killall -15 $fpm
