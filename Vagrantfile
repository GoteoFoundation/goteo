# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"
ENV['VAGRANT_DEFAULT_PROVIDER'] = 'virtualbox'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    # Prevent TTY Errors (copied from laravel/homestead: "homestead.rb" file)... By default this is "bash -l".
    config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
    config.vm.box = "ubuntu/xenial64"
    config.vm.network "forwarded_port", guest: 80, host: 8080 # phpmyadmin
    config.vm.network "forwarded_port", guest: 8081, host: 8081 # goteo test web
    config.vm.network "forwarded_port", guest: 35729, host: 35729 # live reload port
    config.vm.network "forwarded_port", guest: 3306, host: 3307
    config.vm.synced_folder ".", "/home/vagrant/goteo"
    config.vm.provision "shell", path: "vagrant.sh"
end
