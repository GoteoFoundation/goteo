#!/bin/bash

#autochange to development dir on login
cd ~/goteo
if [ $(grep -c 'cd ~/goteo' ~/.bashrc) == '0' ]; then
    echo 'cd ~/goteo' >> ~/.bashrc
    echo 'export GOTEO_CONFIG_FILE=~/goteo/config/local-vagrant-settings.yml' >> ~/.bashrc
    # create config file
    cp config/vagrant-settings.yml config/local-vagrant-settings.yml
    export GOTEO_CONFIG_FILE=~/goteo/config/local-vagrant-settings.yml
    # Create config file for apache
    cat config/vagrant-settings.yml | sed -e "s/8081/8080/" > config/apache-vagrant-settings.yml
    # install composer
    composer install
    # install database
    ./bin/console migrate install
    # install npm modules
    npm install
    echo -e "\e[32mDone!\e[0m"
    echo ""
    echo "To start a development server run these commands:"
    echo ""
    echo -e "\e[32mvagrant ssh\e[0m"
    echo -e "\e[32mgrunt serve\e[0m"
    echo ""
    echo "Then, your browser should be opened automatically pointing"
    echo -e "at \e[36mhttp://localhost:8081\e[0m "
    echo ""
    echo "Optionally, the internal Vagrant's Apache server is configured to serve"
    echo -e "the content of the \e[36m/home/vagrant/goteo/dist\e[0m folder"
    echo "This allows you to test the distribution package."
    echo "To test it, you NEED TO RUN these commands:"
    echo ""
    echo -e "\e[32mvagrant ssh\e[0m"
    echo -e "\e[32mgrunt build:dist\e[0m"
    echo ""
    echo -e "Then, point your browser to \e[36mhttp://localhost:8080\e[0m"
    echo ""
fi


