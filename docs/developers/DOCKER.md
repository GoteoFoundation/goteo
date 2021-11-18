---
currentMenu: docker
---
Docker
======

Docker is the recommended way to setup a development machine as it has all necessary tools already installed. 

To install `docker` and `docker-compose` follow the instructions:

https://docs.docker.com/install/

https://docs.docker.com/compose/install/

We've only tested using Linux hosts, any comments or fixes on other hosts will be appreciated.

---

The first time, it is necessary to create a local docker configuration file that you can personalize:

```bash
cp config/docker-settings.yml config/local-docker-settings.yml
```

Then, you can set up a development server using:

```bash
docker/up
```

`docker/up` is a wrapper for `docker-compose`, you can use any command line modificator (ie: `docker/up -d` instead of `docker-compose -d`).

> 👉 We use the wrapper `docker/up` because it automatically tries to export your user ID to the docker container. Otherwise all generated files by the php server will be owned by another user or root.
> 
> 👉 **This script assumes to run `docker-compose` without root privileges**. Its important for the user running docker to belong to the `docker` group. As is explained in the [Post-installation steps in docker.com](https://docs.docker.com/install/linux/linux-postinstall/) (just run `sudo usermod -aG docker $USER` to be sure).
> 
> In any case, to ensure compatibility with the traditional `docker-compose up`, all created files inside the php container will have full-writeable permissions (ie: folder 777 and files 666)
> 
> 👉 You can specify a custom `docker-compose.yml` file if necessary. Just copy it or create a new one with the name `docker-compose-local.yml`, this file is listed in the `.gitignore` and won't be committed.
> 
> 👉 When updating from older versions (with other configurations of the `docker-compose.yml` file) it maybe necessary to rebuild the containers. To do that just run the same command with the modifier `--build`:
> 
> ```bash
> docker/up --build
> ```
> 


At this point you should be able to point your browser to http://localhost:8081 (or whatever host name you have in your local-docker-settings.yml).

**The correct URL will be shown in the docker-compose log when all preparation commands finish**

We recommend not to use the `-d` flag on `docker-compose` to be aware of the log messages while building the container or php/server errors while browsing.

# TL;TR

1. [How to use Docker](#how-to-use-docker)
1. [Upgrade Goteo](#upgrade-goteo)
2. [SSL configuration for development](#ssl-configuration)
3. [Mailhog: Development mail debugging](#mailhog-development-mail-debugging)
4. [Cron: background processes](#cron-background-processes)
5. [Geolocation](#geolocation)
6. [Debugging logs](#debugging-logs)

## How to use Docker

The `docker/up` wrapper export the variable of the current user in order to match the UID of the docker user. This way we avoid permission problems while developing. For more info check this post: https://denibertovic.com/posts/handling-permissions-with-docker-volumes/

The `docker-compose up` command executes `docker/php/init.sh` script, which is equivalent as running the next commands:

```bash
docker/exec composer install
docker/exec npm install
docker/exec bin/console migrate install
docker/exec grunt build:tmp
```

You can (or must) run any of the above commands if the are changes in relevant files (database changes, css, javascript or public template files).

In general, any command used in Goteo to be executed in the docker virtual machine should use the wrapper `docker/exec` as it will run the command with the proper user.

If you want to test a production environment, you can pass the shell variable `DEBUG=0` to the docker-compose command:

```bash
DEBUG=false docker/up
```

You can overwrite the default `local-docker-settings.yml` file with the GOTEO_CONFIG_FILE environment variable:

```bash
GOTEO_CONFIG_FILE=config/my-alternative-settings.yml docker/up
```

Finally -optionally-, by running the `grunt watch` command alone allows you to rebuild assets automatically while editing files. Note that assets are always recompiled when docker starts.

```bash
docker/exec grunt watch
```

Alternatively, you can just build the assets any time some css or javascript file has been modified by executing:

```bash
docker/exec grunt build:tmp
```

Upgrades and other commands can be executed the same way:

```bash
docker/exec bin/console migrate all
docker/exec bin/console toolkit project
docker/exec bin/console --help
```

### Upgrade Goteo

If using Git, you can upgrade to any new version of Goteo pulling the new code:

```
git clone git@github.com:GoteoFoundation/goteo.git
git pull origin devel
docker/up --rebuild
```

### SSL configuration

The Docker image comes with (experimental) support for SSL. However, to use it you need to:

1. Use a **test domain** supported by the certificate and configure your `/etc/hosts` to point it to your machine, for example `goteo.test`.
  Add a line to your `/etc/hosts` file like this:

```
127.0.0.1 localhost goteo.test www.goteo.test ca.goteo.test en.goteo.test es.goteo.test
```

2. Change the URL in your `local-docker-settings.yml` settings file, use `//goteo.test:8443` instead of `localhost:8081`. Something like:

  ```yaml
  # url
  url:
      main: //goteo.test:8443
      assets: //goteo.test:8443
      # optionally:
      url_lang: goteo.test:8443
  ...
  ```

3. Configure Chrome to trust the Docker certificate in order to show the site as secure. To add the certificate to the trusted CA root store you can just do (in Ubuntu you need `libnss3-tools` installed):

  ```bash
  sudo apt install libnss3-tools
  certutil -d sql:$HOME/.pki/nssdb -A -t "P,," -n "localhost" -i docker/certs/localhost.crt
  ```


### Mailhog: Development mail debugging

[Mailhog](https://github.com/mailhog/MailHog) is an email testing tool for developers. It creates a fake smtp server that caches all messages and uses a web interface to debug them.

Goteo docker is preconfigured out of the box to use Mailhog, you just need to point your browser to the address http://localhost:8082 and view the email generated by the platform.

> Some emails are sent by the cron process. Docker does not run cron by default. You can manually trigger the mailing process by executing the command:
>
> ```bash
> docker/exec bin/console mailing last -u
> ```

### Cron: background processes

Goteo uses some background processes to certain task. For instance, sending newsletters, emails to several users, control project statuses daily or process payment refunds.

For developing is usually interesting to trigger this events manually, you can execute cron in a terminal with this command:

```bash
docker/exec bin/console cron
```

All pending tasks will be executed.

### Geolocation

Some actions need geolocation services. Docker uses a container with the [Maxmind Geolite2](https://dev.maxmind.com/geoip/geoip2/geolite2/) database configured. 

This databases are updated every time docker starts. If for some reason you want to update them manually, just run:

```bash
docker-compose up geoip
```

### Debugging logs

Goteo, by default, generates Logs in JSON format. This is convenient for machine processing but it may be a little confusing. The Docker image comes with the [jq](https://stedolan.github.io/jq/) parser program installed.

You can filter some logs and prettify them by doing thinks like:

Enter into the container:

```bash
docker/exec bash
```

Once inside, use  `tail`, `jq` and `grep` for fitering:

```
tail -n100 var/logs/app_real.log | grep INFO | jq
tail -f var/logs/app_real.log | grep -v DEBUG | jq
```

### Running tests

You can run test by using the wrapper `docker/test`. Arguments are the same as phpunit.

**It is recommended to create a test file the first time.**

#### Preparation:

In order to run test using docker, you need to have the containers up and running.

So first, start the normal docker environment, and keep it opened in an independent terminal:

```
docker/up
```

On a different terminal, generate the test settings file:

```bash
cp config/local-docker-settings.yml config/test-docker-settings.yml
```

Edit the database setting, and put `goteo_test` in it:

```yaml
# Database stuff
db:
    driver:   mysql     # Database driver (mysql)
    host:     mariadb # Database host
    port:     3306      # Database port
    charset:  utf8mb4     # Database charset
    database: goteo_test     # Database schema (database name)
    username: goteo      # Database user for the goteo database
    password: goteo      # Password for the goteo database
```

Then, create the database and grant permissions in the mariadb container:

```bash
docker-compose exec mariadb mysql -uroot -pcrowdfunding -e 'CREATE DATABASE goteo_test;'
docker-compose exec mariadb mysql -uroot -pcrowdfunding -e "GRANT ALL PRIVILEGES ON goteo_test.* TO 'goteo'@'%';"
```

Finally, reset the database:

```bash
docker/test -r
```

---

From now on, just run tests as:

```bash
docker/test tests/Goteo/Core/ModelTest.php
docker/test tests/
...
```
