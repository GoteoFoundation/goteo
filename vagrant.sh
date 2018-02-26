#!/bin/bash
# Update server
export DEBIAN_FRONTEND=noninteractive
echo "set grub-pc/install_devices /dev/sda" | debconf-communicate
apt-get -y update
apt-get -y upgrade
# Install essentials
apt-get -y install build-essential binutils-doc libssl-dev git -y
# Install Apache
apt-get -y install apache2 libapache2-mod-ruid2
#Install PHP
apt-get -y install php libapache2-mod-php php-cli php-gd php-mcrypt php-mysql php-curl php-xdebug
apt-get -y install nodejs npm
apt-get -y install ruby-dev rubygems-integration
# Install MySQL
echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
apt-get -y install mysql-client mysql-server
# Install PhpMyAdmin
echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/app-password-confirm password root' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/admin-pass password root' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/mysql/app-pass password root' | debconf-set-selections
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
apt-get -y install phpmyadmin

# disable default apache site
a2dissite 000-default
# copy the apache configuration
cp /home/vagrant/goteo/var/php/apache2.4.conf /etc/apache2/sites-enabled/goteo.conf
# enable suexec
a2enmod ruid2

# Restart Apache service
service apache2 restart

# fully accessible database
cat /etc/mysql/my.cnf | sed -e "s/bind-address$(printf '\t\t')= 127.0.0.1/#bind-address        = 127.0.0.1/" > /etc/mysql/my.cnf
mysql -proot -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root';"
iptables -F
iptables-save
iptables -t nat -A PREROUTING -i lo -p tcp --dport 3307 -j REDIRECT --to-port 3306
iptables-save
service mysql restart
mysql -proot -e 'CREATE DATABASE IF NOT EXISTS goteo;'

# sass css preprocessor
gem install sass -v 3.4.23
gem install compass
# Grunt
npm install -g grunt-cli
# create compatibitly link for node
if [ ! -f /usr/local/bin/node ]; then
    ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Composer globally
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

#autochange to development dir on login
su -c "source /home/vagrant/goteo/vagrant-user-config.sh" vagrant

