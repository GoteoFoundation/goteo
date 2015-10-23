Goteo The Open Source Crowdfunding Platform
===========================================

**THIS IS IN ALPHA STATUS**, Do no try it on to a production site.

This release is only for developers and for those who want to collaborate with the code.

Full developers documentation is not ready yet, sorry!
Some folders has his own README.md file with comments.

License
-------

The code licensed here under the **GNU Affero General Public License**, version 3 AGPL-3.0 has been developed by the Goteo team led by Platoniq and subsequently transferred to the Fundación Goteo, as detailed in http://www.goteo.org/about#info6

This is a web tool that allows the receipt, review and publishing of collective campaigns for their collective funding and the receiving of collaborations as well as the dynamic visualization of the support received, classification of initiatives and campaign tracking. The system also permits secure and distributed communication with users and between users, administration of highlighted projects on the home page and the creation of periodical publications such as blogs, a FAQ section and static pages.


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
    charset:  UTF-8     # Database charset
    database: goteo     # Database schema (database name)
    username: root     # Database user for the goteo database
    password: root  # Password for the goteo database
```


Otherwise, you can install mysql on your own machine and proceed to import the database into it.

TODO...

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

* **Run phpunit**: `grunt deploy`
  Can be used to upload files assets to Amazon S3

* **Run phpunit**: `grunt phpunit`

* **Default task**: `grunt`
  This task is the same as execute as doing: `grunt lint`
  It performs static code analysis in order to quick detect mistakes or misspellings

* **Code linter**: `grunt lint`
  Same as default


Server configuration
---------------------

Goteo has been tested under Nginx and Apache configurations.

You will need to build a `dist` production folder an point the webserver onto it. Use `grunt build` to create a minified assets enviroment or `grunt build:devel` for javascript/css debugging.

### Apache config:

Modrewrite must be enabled for apache below 2.2.16:

```
<IfModule mod_rewrite.c>
    Options -MultiViews

    RewriteEngine On
    #RewriteBase /path/to/goteo-folder/dist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

Alternatively, if you use Apache 2.2.16 or higher, you can use the FallbackResource directive to make your .htaccess even easier:

```
FallbackResource /index.php
```

### Nginx config:

```
server {
    server_name domain.tld www.domain.tld;
    root /var/www/goteo-folder/dist;

    location / {
        # try to serve file directly, fallback to front controller
        try_files $uri /index.php$is_args$args;
    }

    # If you have 2 front controllers for dev|prod use the following line instead
    # location ~ ^/(index|index_dev)\.php(/|$) {
    location ~ ^/index\.php(/|$) {
        # the ubuntu default
        fastcgi_pass   unix:/var/run/php5-fpm.sock;
        # for running on centos
        #fastcgi_pass   unix:/var/run/php-fpm/www.sock;

        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;

        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Enable the internal directive to disable URIs like this
        # internal;
    }

    #return 404 for all php files as we do have a front controller
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/project_error.log;
    access_log /var/log/nginx/project_access.log;
}
```

