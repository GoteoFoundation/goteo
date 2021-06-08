####################################
# PHPDocker.io PHP 7.1 / FPM image #
####################################

FROM ubuntu:bionic-20210512
WORKDIR /application

# Add Ondrej APT reposiroty to have PHP 7.1
RUN apt-get update
RUN apt-get -y install apt-utils software-properties-common wget apt-utils curl
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
RUN add-apt-repository ppa:ondrej/php
RUN apt-key update

# Install FPM
RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update \
    && apt-get -y --no-install-recommends install php7.1-fpm php7.1-memcached php7.1-mysql php7.1-gd php7.1-xdebug \
    && apt-get -y --no-install-recommends install php7.1-curl php7.1-dom php7.1-xml php7.1-json php7.1-mbstring \
    && apt-get -y --no-install-recommends install php7.1-zip php7.1-readline php7.1-sodium \
    && apt-get -y --no-install-recommends install nodejs npm build-essential ruby-dev rubygems-integration gosu \
    && apt-get -y install zip unzip \
    && apt-get -y install mysql-client \
    && apt-get -y install jq \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN gem install sass -v 3.4.23
RUN gem install compass
RUN npm install -g grunt-cli@1.3.2

# PHP-FPM packages need a nudge to make them docker-friendly
COPY overrides.conf /etc/php/7.1/fpm/pool.d/z-overrides.conf

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
