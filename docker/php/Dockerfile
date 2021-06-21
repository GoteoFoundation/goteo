####################################
# PHPDocker.io PHP 7.1 / FPM image #
####################################

FROM ubuntu:bionic
WORKDIR /application
ARG PHP_VERSION=7.1

# Install basic packages
RUN apt-get update
RUN apt-get -y install apt-utils software-properties-common wget apt-utils curl zip unzip mysql-client jq

# Add Ondrej APT repository to have install several PHP versions
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
RUN add-apt-repository ppa:ondrej/php
RUN apt-key update

# Install FPM
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update \
    && apt-get -y --no-install-recommends install php${PHP_VERSION}-fpm php${PHP_VERSION}-memcached \
    && apt-get -y --no-install-recommends install php${PHP_VERSION}-mysql php${PHP_VERSION}-gd php${PHP_VERSION}-xdebug \
    && apt-get -y --no-install-recommends install php${PHP_VERSION}-curl php${PHP_VERSION}-dom php${PHP_VERSION}-xml \
    && apt-get -y --no-install-recommends install php${PHP_VERSION}-json php${PHP_VERSION}-mbstring \
    && apt-get -y --no-install-recommends install php${PHP_VERSION}-zip php${PHP_VERSION}-readline \
    && apt-get -y --no-install-recommends install nodejs npm build-essential ruby-dev rubygems-integration gosu \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "copy('https://composer.github.io/installer.sig', 'installer.sig');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN gem install sass -v 3.4.23
RUN gem install compass
RUN npm install -g grunt-cli@1.3.2

# PHP-FPM packages need a nudge to make them docker-friendly
COPY overrides.conf /etc/php/${PHP_VERSION}/fpm/pool.d/z-overrides.conf

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
