Goteo The Open Source Crowdfunding Platform
===========================================

Goteo is a web app using PHP/Javascript/Mysql. 

## Development enviroment

We use the **grunt** tool in order to execute repetitive task such as:
* Javascript minification
* CSS minification
* Image optimizations
* Package installers
* Code checks and tests
* ... so on ...

**Composer** is the most popular PHP package management, we use it too.

## Setting up environment

You can set-up a development environment in your local machine by installing all required tools. Or, you can use a convenient Vagrant virtual machine with all tools ready to go.

To install vagrant please refer to the official web site:

http://www.vagrantup.com/downloads

You may install virtualbox as well:

https://www.virtualbox.org/wiki/Downloads

Using Vagrant Virtual Machine
=============================

The Vagrant file provided automatically configures a virtual machine with all necessary tools.

Just open a terminal where you have your copy of Goteo code and execute:

Start the virtual machine (it will be a while first time you do that):

```bash
vagrant up
```

You need to log into the virtual machine to start the development server:

```bash
vagrant ssh
cd /home/vagrant/goteo
```

First time (or when dependencies are updated), you need to run:

Node and composer dependencies:

```bash
npm install
composer install
```

Create a settings file:

```bash
cp config/vagrant-settings.yml config/settings.yml
```

Import a starting database to the server:
**TODO!!!!**

```
mysql ...
```

Start the development server:

```bash
grunt serve
```

You can do the last command in your own machine, then vagrant will be used for mysql only.

Now you can open your favorite browser on your machine and go to:

`http://localhost:8081`

A copy of PhpMyAdmin is also running on the virtual machine, just go to:

`http://localhost:8080/phpmyadmin`

Installing everything in your own machine
=========================================

## Installing grunt

Grunt is a scripting task tool installable trough npm, the Node.js.
Please refer to the official guide to grunt to install it:

http://gruntjs.com/getting-started

If you want to install grunt on Ubuntu 12.04 o4 14.04 for the very first time just do:
```bash
sudo apt-get install build-essential libssl-dev git
sudo add-apt-repository ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install nodejs
sudo npm install -g grunt-cli
```

Additionally, you may remove the configuration npm user directory to avoid
unexpected permissions problems when using npm as non root user afterwards:

```bash
sudo rm ~/.npm -rf 
```

## Installing composer

TODO

## Installing mysql

Instead of installing MySQL on your own machine, you may consider to install vagrant and use it as your msyql local server.

TODO

If you have copied the vagrant-settings.yml as you initial starting settings:
- change in settings.yml db port from 3306 to 3307, localhost to 0.0.0.0
- change in settings.yml url port from 8082 to 8081
```
url:
    main: //localhost:8081
    # url de recursos estaticos (imagenes, CSS)
    assets: //localhost:8081

...

# Database stuff
db:
    driver:   mysql     # Database driver (mysql)
    host:     0.0.0.0 # Database host
    port:     3307      # Database port
    charset:  UTF-8     # Database charset
    database: goteo     # Database schema (database name)
    username: root     # Database user for the goteo database
    password: root  # Password for the goteo database
```


Otherwise, you can install mysql on your own machine and proceed to import the database into it.

TODO

Using grunt in Goteo
====================

Once you have your copy of grunt installed you need to install the tasks used.
To do so you may just execute in the path where you have your copy of the Goteo code:

```bash
npm install
```

After that you're ready to execute any of the task available in the same directory:

```bash
grunt jshint
grunt phplint
...
``` 


## Grunt commands in Goteo

* **Default task**: `grunt`
  This task is the same as execute as doing: `grunt lint`
  It performs static code analysis in order to quick detect mistakes or misspellings

* **Code linter**: `grunt lint`
  Same as default
