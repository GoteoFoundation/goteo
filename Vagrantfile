# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"
ENV['VAGRANT_DEFAULT_PROVIDER'] = 'virtualbox'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.vm.box = "trusty-server"
    config.vm.box_url = "https://oss-binaries.phusionpassenger.com/vagrant/boxes/latest/ubuntu-14.04-amd64-vbox.box"
    config.vm.network "forwarded_port", guest: 80, host: 8080
    config.vm.network "forwarded_port", guest: 8081, host: 8082, auto_correct: true
    config.vm.network "forwarded_port", guest: 3306, host: 3307
    config.vm.synced_folder ".", "/home/vagrant/goteo"
    config.vm.provision "shell", path: "vagrant.sh"
end
