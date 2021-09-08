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

You can set-up a development environment in your local machine by installing all required tools. Or, you can use a Docker virtual machine with all tools ready to go.

To install `docker` and `docker-compose` follow the instructions:

https://docs.docker.com/install/
https://docs.docker.com/compose/install/

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

The whole section has been moved to the [Docker section](http://goteofoundation.github.io/goteo/docs/developers/docker.html)
