---
currentMenu: environment
---
## Development environment

Goteo is a web app using PHP/Javascript/Mysql. 

We use the **grunt** tool in order to execute repetitive task such as:
* Javascript minification
* CSS minification
* Image optimizations
* Package installers
* Code checks and tests
* ... so on ...

**Composer** is the most popular PHP package management, we use it too.

## Setting up environment

You can set-up a development environment in your local machine by installing all required tools. Or, you can use either a Vagrant or Docker virtual machine with all tools ready to go.

To install `docker` and `docker-compose` follow the instructions:

https://docs.docker.com/install/
https://docs.docker.com/compose/install/

To install vagrant refer to the official web site:

http://www.vagrantup.com/downloads

You may install virtualbox as well:

https://www.virtualbox.org/wiki/Downloads

Using Vagrant Virtual Machine
=============================

*NOTE:* We think that docker is a better way to quickly install Goteo as it uses much less resources, probably we will deprecate the Vagrant development aproach in the future.

The Vagrant file provided automatically configures a virtual machine with all necessary tools.

Just open a terminal where you have your copy of Goteo code and execute:

Start the virtual machine (it will be a while first time you do that):

```bash
vagrant up
```

You need to log into the virtual machine to start the development server:

```bash
vagrant ssh
```

The first time, a vagrant local config file will be create a a `config/local-vagrant-settings.yml`. The env variable GOTEO_CONFIG_FILE will be append to the .bashrc file in vagrant poining to that file. Database will be autocreated and dependencies installed.

You just need to log into vagrant and start the development server:

```bash
grunt serve
```

**Vagrant Apache dist testing**

It is possible to build the distribution goteo package (for production sites) and test it in the same vagrant installation which is already configured to serve the content of the folder `/home/vagrant/goteo/dist`.

Try it on by building the package (remember to log into vagrant first):

```bash
grunt build:dist
```

And, then pointing your browser to http://localhost:8080/
You can tweak the Apache configuration in `/etc/apache2/sites-enabled/goteo.conf` (inside vagrant)


**Code updates**

To update composer or npm dependencies when code is updated, you just need to log into vagrant (`vagrant ssh`) and run these commands:

Node and composer dependencies:

```bash
cd /home/vagrant/goteo
npm install
composer install
```

It's also safe to run the MySQL migration tool to ensure database version is up to date:

```bash
./bin/console migrate all
```


You can do the `grunt serve` command in your own machine, then vagrant will be used for mysql only.

Now you can open your favorite browser on your machine and go to:

`http://localhost:8081`

Login with user "root" and password "root" (no quotes).
Go `http://localhost:8081/admin/users/edit/root` to change the password and email

A copy of PhpMyAdmin is also running on the virtual machine, just go to:

`http://localhost:8080/phpmyadmin`

Installing everything in your own machine
=========================================

## Installing grunt

Grunt is a scripting task tool installable trough npm, the Node.js.
Please refer to the official guide to grunt to install it:

http://gruntjs.com/getting-started

If you want to install grunt on Ubuntu 16.04 for the very first time just do:

```bash
sudo apt install build-essential libssl-dev git
sudo apt install nodejs npm
sudo apt install ruby-dev rubygems-integration
sudo gem install sass -v 3.4.23
sudo gem install compass
sudo npm install -g grunt-cli
```

If you want to install grunt on Ubuntu 12.04 or 14.04 for the very first time just do:

```bash
sudo apt-get install build-essential libssl-dev git
sudo add-apt-repository ppa:chris-lea/node.js
sudo apt-get update
sudo apt-get install nodejs
sudo apt-get install rubygems-integration
sudo gem install sass -v 3.4.23
sudo gem install compass
sudo npm install -g grunt-cli
```

Additionally, you may remove the configuration npm user directory to avoid
unexpected permissions problems when using npm as non root user afterwards:

```bash
sudo rm ~/.npm -rf
```

## Installing composer

To install Composer on Ubuntu or any other *nix execute this commands:

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

For other OS, please refer to the official install guides:
Install Composer: https://getcomposer.org/doc/00-intro.md


## Installing mysql

Instead of installing MySQL on your own machine, you may consider to install vagrant and use it as your msyql local server.

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
    charset:  utf8mb4     # Database charset
    database: goteo     # Database schema (database name)
    username: root     # Database user for the goteo database
    password: root  # Password for the goteo database
```


Otherwise, you can install mysql on your own machine and proceed to import the database into it.

<a name="grunt"></a>
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

Grunt commands in Goteo
-----------------------

* **Build**: `grunt build`
  This will create a `dist` folder ready to point a web server (apache, nginx) onto it

* **Development Build**: `grunt build:devel`
  Same as build, but the files created into `dist` will not be minimized

* **Development server**: `grunt serve`
  Php standalone server. For localhost developing.

* **Multi-threading development server**: `grunt serve:nginx`
  For localhost developing as well. But you need to have nginx installed on your machine, it may speed up significantly the development process.

* **Deploy assets**: `grunt deploy`
  Can be used to upload files assets to Amazon S3 (settings.yml must be configured)

* **Run phpunit**: `./run-tests.sh`
  bash script to wrap phpunit tests (run it with `--help` for more info)

* **Default task**: `grunt`
  This task is the same as execute as doing: `grunt lint`
  It performs static code analysis in order to quick detect mistakes or misspe -hllings

* **Code linter**: `grunt lint`
  Same as default


<a name="docker"></a>
Docker
======

We just started to work with Docker. For the moment is still experimental. We've only tested using Linux hosts.

The first time, it is necessary to create a local docker config that you can personalize:

```bash
cp config/docker-settings.yml config/local-docker-settings.yml
```

Then, you can set up a development server using:

```bash
docker-compose up
```

At this point you should be able to point your browser to http://localhost:8081 (or whatever host name you have in your local-docker-settings.yml).
We recommend not to use the `-d` flag on `docker-compose` to be aware of the log messages while building the container or php/server errors while browsing.

### TL;TR

The `docker-compose up` command executes `docker/php/init.sh` script, which is equivalent as running the next commands:

```bash
docker/exec composer install
docker/exec npm install
docker/exec bin/console migrate install
docker/exec grunt build:tmp
```

You can (or must) run any of the above commands if the are changes in relevant files (database changes, css, javascript or public template files).

In general, any command used in goteo should be executed in the docker virtual machine should by using the wrapper `docker/exec` as it will run the command with the proper user.

If you want to test a production environment, you can pass the var `DEBUG=0` to the docker-compose command:

```bash
DEBUG=false docker-compose up
```

You can overwrite the default `local-docker-settings.yml` file with the GOTEO_CONFIG_FILE environment variable:

```bash
GOTEO_CONFIG_FILE=config/my-alternative-settings.yml docker-compose up
```


Finally -optionally-, by running the `grunt watch` command alone allows you to rebuild assets automatically while editing files. If this command is not executed, assets are copied and compiled only once at the beginning when `docker-compose up` runs.

```bash
docker/exec grunt watch
```

Upgrades and other commands can be executed the same way:

```bash
docker/exec bin/console migrate all
docker/exec bin/console toolkit project
docker/exec bin/console --help
```
