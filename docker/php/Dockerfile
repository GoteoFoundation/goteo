####################################
# PHPDocker.io PHP 7.4 / FPM image #
####################################

FROM phpdockerio/php74-cli
WORKDIR /application

# Install FPM
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update \
    && apt-get -y install apt-utils \
    && apt-get -y --no-install-recommends install php7.4-fpm php7.4-mcrypt \
    && apt-get -y --no-install-recommends install php7.4-intl php7.4-memcached php7.4-mysql php7.4-gd php7.4-xdebug \
    && apt-get -y --no-install-recommends install nodejs npm build-essential ruby-dev rubygems-integration gosu \
    && apt-get -y install zip unzip \
    && apt-get -y install mysql-client \
    && apt-get -y install jq \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN gem install sass -v 3.4.23
RUN gem install compass
RUN npm install -g grunt-cli@1.3.2

# PHP-FPM packages need a nudge to make them docker-friendly
COPY overrides.conf /etc/php/7.4/fpm/pool.d/z-overrides.conf

# PHP-FPM has really dirty logs, certainly not good for dockerising
# The following startup script contains some magic to clean these up
COPY php-fpm-startup /usr/bin/php-fpm

# Entrypoint to execute commands with the host user
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Open up fcgi port
EXPOSE 9000
EXPOSE 35729

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Will be overwritten by docker-compose.yml
CMD /usr/bin/php-fpm
