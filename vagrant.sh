#!/bin/bash
# Update server
echo "set grub-pc/install_devices /dev/sda" | debconf-communicate
apt-get install software-properties-common python-software-properties -y
add-apt-repository ppa:chris-lea/node.js
apt-get update
apt-get upgrade -y
# Install essentials
apt-get -y install build-essential binutils-doc libssl-dev git -y
# Install Apache
apt-get install apache2 -y
#Install PHP
apt-get install php5 libapache2-mod-php5 php5-cli php5-mysql php5-curl -y
# Install MySQL
echo "mysql-server mysql-server/root_password password root" | sudo debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | sudo debconf-set-selections
apt-get install mysql-client mysql-server -y
# Install PhpMyAdmin
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password root' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password root' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password root' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
apt-get install phpmyadmin -y
# Restart Apache service
service apache2 restart

# fully accessible database
cat /etc/mysql/my.cnf | sed -e "s/bind-address$(printf '\t\t')= 127.0.0.1/#bind-address        = 127.0.0.1/" > /etc/mysql/my.cnf
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root';" -proot
service mysql restart

# NPM
apt-get install nodejs -y
npm install -g grunt-cli

# Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

#autochange to development dir on login
if [ $(grep -c 'cd ~/goteo' .bashrc) == '0' ]; then
    echo 'cd ~/goteo' >> /home/vagrant/.bashrc
fi

