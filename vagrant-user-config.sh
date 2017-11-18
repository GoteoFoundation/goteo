#!/bin/bash

#autochange to development dir on login
cd ~/goteo
if [ $(grep -c 'cd ~/goteo' ~/.bashrc) == '0' ]; then
    echo 'cd ~/goteo' >> ~/.bashrc
    echo 'export GOTEO_CONFIG_FILE=~/goteo/config/local-vagrant-settings.yml' >> ~/.bashrc
    # create config file
    cp config/vagrant-settings.yml config/local-vagrant-settings.yml
    export GOTEO_CONFIG_FILE=~/goteo/config/local-vagrant-settings.yml
    # install composer
    composer install
    # install database
    ./bin/console migrate install
    # install npm modules
    npm install
    echo "Done!\nexec 'vagrant ssh' and 'grunt serve' to start"
fi


