---
currentMenu: install
---
Installation
============


Server requirements
-------------------

- PHP v5.6 or later with most common extension activated (GD, mcrypt, etc)
- Apache, Nginx or any other server with ModRewrite activated
- MySQL 5.6 or later

Server configuration
---------------------

Goteo has been tested under Nginx and Apache configurations.

> At this point, there are still no production-ready releases. Please refer to the [developers](developers/environment.html) documentation for more info.
>
> You will need to install dependencies and to build a `dist` production folder to point the webserver onto it.
>
> We use [Composer](https://getcomposer.org/) and [Grunt](http://gruntjs.com/) for that. [Be sure to have it installed in your system](developers/environment.html).
>
> **Quick start**
>
> Install all the required dependencies:
>
> ```bash
> composer install
> npm install
> ```
>
> Create the compiled minimized system:
>
> ```bash
> grunt build
> ```
>
> Alternatively, use  `grunt build:devel` for javascript/css debugging.



### Apache config:

Modrewrite must be enabled for apache below 2.2.16:

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews

    RewriteEngine On
    #RewriteBase /path/to/goteo-folder/dist
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [QSA,L]
</IfModule>
```

Alternatively, if you use Apache 2.2.16 or higher, you can use the FallbackResource directive to make your .htaccess even easier:

```apache
FallbackResource /index.php
```

> **NOTE:**
> - If you want to debug the site, you must point the server to the `index_dev.php` file instead of `index.php`. This way error traces will be shown in the error pages
> - If you cannot configure the server to point to the `dist/` folder, the `.htaccess` file on the root folder can be used as alternative (using this solution will force the use of an assets URL pointing to the dist/ folder.)

### Nginx config:

```nginxs
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

Database configuration
----------------------

First you'll need an empty database (**your_goteo_db**) with user (**your_user**) and password (**your_password**).

Once you got it, a minimal system must be created, import this 3 SQL scripts:

- `db/install/structure.sql`
- `db/install/data.sql`
- `db/install/templates.sql`

**NB:** Run `db/install/upgrade-to-v3.0.9.sql` first before `db/install/data.sql`

Both scripts should be applied in a MySQL console:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/install/structure.sql
```

Then, apply the minor-version update:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/install/upgrade-to-v3.0.9.sql
```

And, finally, import some bare data (glossary and faq may be optional, or you can delete date afterwards):

```bash
mysql -u your_user -pyour_password your_goteo_db < db/install/data.sql
mysql -u your_user -pyour_password your_goteo_db < db/install/templates.sql
mysql -u your_user -pyour_password your_goteo_db < db/install/glossary.sql
mysql -u your_user -pyour_password your_goteo_db < db/install/faq.sql
```


This should give you an empty system with only one user "root".

Login with user **"root"** and password **"root"** (no quotes).
Go `http://your.intallation.site/admin/users/edit/root` to change the password and email
Try login with that password and manage the contents at http://your.intallation.site/admin

The `data.sql` script also creates demo project, you can remove all demo data by using the command `./bin/console dev:statusinit --erase'` from the [dev-plugin](developers/extend.html#dev-plugin).

However, we haven't tested it in any production database and very likely errors will be thrown. Debugging info will be appreciated.

Cron configuration
---------------------

You will need to add a cron/crontab line in order to process severals project related events:

```cron
* * * * *   nice /usr/bin/php /path/to/installation/bin/console cron --lock --logmail > /dev/null 2>&1
```

Refer to the **Console scripts** documentation for more info

Goteo configuration
-------------

Place in here a non-git-tracked copy of your `settings.yml`

Use `demo-settings.yml` or `vagrant-settings.yml` as a example.

Particularly you must configure the `db:` section with the proper MySQL username/password on settings.yml

You can specify a different `dev-settings.yml` file which will be used by `public/index_dev.php`


Example installation `config/settings.yml`:

```yaml
# Goteo settings file
---

# Maintenance
maintenance: false

# Max session time
session:
    time: 3600

# Internal secret for hashes
secret: --a-very-secret-string---

# local enviroment: local, beta, real
env: local
debug: true # whether to show trace for errors or not
            # This is always true for local or beta env
# liveport: 35729 # Local development only livereload port (optional)


# main node
node: goteo

# Default system language
lang: es

# Default timezone
timezone: Europe/Madrid

# url
url:
    main: //example.com
    # static resources url if you use a different assets server
    assets: //example.com
    #optional, configure this as hostname only (ex: example.com) if you want languages to be selected as subdomains (es.example.com, en.example.com)
    url_lang: example.com
    
    # If you want to use a CDN or another web server to serve the cached images
    # You can define this constants. All cached images links will point to this
    # Url, event if don't exists yet.
    # Example:
    # Point a data.example.com to var/cache/images
    # the place a .htaccess in var/cache/images/.htaccess with this content
    # <ifmodule mod_rewrite.c>
    # RewriteEngine on
    # RewriteCond %{REQUEST_FILENAME} !-f
    # RewriteCond %{REQUEST_FILENAME} !-d
    # RewriteRule ^(.*) http://example.com/img/$1 [R=302,L]
    # </ifmodule>
    #
    data:

# PLUGINS
# Extend is the directory to personalize your copy of goteo.org
# Routes, classes and templates can be overridden by copying the main structure
# ie:
#    - Autoloading classes
#    - Templates will be found first from extend/plugin-name/Resources/templates path
#    - Tests should be placed into extend/plugin-name/tests path
#    - Routes can be overwritten
#    - Service container can be tampered

plugins:
    goteo-dev:
        active: true # plugin should be active=true to be loaded

# Payment methods, must be registered as classes implementing Goteo\Payment\Method\PaymentMethodInterface
payments:
    # Paypal
    paypal:
        active: true
        testMode:  true # set to false to real checkouts
        username: paypal@example.com
        password: paypal-password
        signature: PAYPAL-Signature
        # brandName: Your organisation
        # headerImageUrl: Some URL image for the header
        # logoImageUrl: logo URL
        # borderColor: B5DADC

    # This is a built-in payment method using internal credit
    pool:
        active: true
    # A stupid payment method defined in the plugin goteo-dev
    # Useful for development and testing
    dummy:
        active: true

    # Additional custom payment methods should be added here

# Force using https protocol
ssl: false

# IMPORTANT! if ssl is true and your server is behind a proxy
# List the trusted proxies for SSL connection here
proxies:
    - 127.0.0.1
    # Cloudflare IP list:
    # - 103.21.244.0/22
    # - 103.22.200.0/22
    # - 103.31.4.0/22
    # - 104.16.0.0/12
    # - 108.162.192.0/18
    # - 141.101.64.0/18
    # - 162.158.0.0/15
    # - 172.64.0.0/13
    # - 173.245.48.0/20
    # - 188.114.96.0/20
    # - 190.93.240.0/20
    # - 197.234.240.0/22
    # - 198.41.128.0/17
    # - 199.27.128.0/21


# Default commission fee
fee: 4

# Filesystem used by goteo
filesystem:
    handler:    local      # 's3' to use AmazonS3 storage, 'local' to use local file system
    # Only need to be defined credentials if file system is s3:
    # AWS credentials
    aws: &aws1
        key:        your-aws-key
        secret:     your-aws-secret
        region:     eu-west-1

    bucket:
        static:     static.example.com        # where to store the assets (css, js, images)
        mail:       mail-archive.example.com  # where to store alternative mail view

# Database stuff
db:
    driver:   mysql     # Database driver (mysql)
    host:     localhost # Database host
    port:     3306      # Database port
    charset:  UTF-8     # Database charset
    database: your_goteo_db     # Database schema (database name)
    username: your_user     # Database user for the goteo database
    password: your_password  # Password for the goteo database

    # SELECT queries caching
    # set it as 'files' to enable sql cache
    cache:
        driver:           # leave empty to avoid query-caching
        time: 5           # time in seconds where SELECT queries will be cached (may be overwritten by Model::query->cacheTime())
        long_time: 3600   # Obsolete

    # Read-only replicas (optional)

    replica:
        host:  # leave empty to not activate replica
        # this parameters are optionals (needed in case replica credentials are different)
        port: 3306
        username: db-replica-username
        password: db-replica-password

# HTML Metas
meta:
    title:       --meta-title--       # Html default <title>
    description: --meta-description-- # Html default <meta description>
    keywords:    --keywords--         # Html default <meta keywords>
    author:      --author--           # Html default <meta author>
    copyright:   --copyright--        # Html default <meta copyright>

# Loggin level
log:
    # app collects general messages generated by de website
    app: info # debug info, warning, error (default)
    # payment collects messages related to payments
    payment: debug
    # Debug level in Console commands
    console: debug
    # mail specifies at what log level the error will be sent to mail.fail email
    mail: error # goes to mail.fail address

# Mail transport
mail:
    # receiving emails
    mail:         info@example.com     # Main
    contact:      info@example.com     # consulting head
    manager:      manager@example.com  # accounts manager
    fail:         dev@example.com      # dev head
    log:          sitelog@example.com  # Loggin mail

    # allowed addresses while in BETA/LOCAL env in PREG format
    beta_senders: "(.+)example\\.org|(.+)example\\.com"
    # Add to BCC this address to all communications (except massive). Applies only in REAL env
    # Not recommended, there's a way in the admin to see all sent communications
    bcc_verifier:

    # Default users where to send project notifications by default
    # Only used if no consultants are assigned
    consultants:
        root: 'Root'

    transport:
        from:          noreply@example.com
        name:          Goteo Sender
        type:          smtp
        # if type is smtp:
        smtp:
            auth:     true
            secure:   ssl
            host:     smtp.example.com
            port:     587
            username: smtp-usermail
            password: smtp-password

    quota:
        total: 50000  # Maximum sending quota in 24 hours time (useful for SMTP servers like Amazon SES)
        sender: 40000 # Part of this quota used by newsletter mailing

    # Amazon SNS key to process automatic bounces: 'arn:aws:sns:us-east-1:XXXXXXXXX:amazon-ses-bounces'
    # URL mus be: goteo_url.tld/aws-sns.php
    sns:
        client_id:        XXXXXXXXX
        region:           us-east-1
        bounces_topic:    amazon-ses-bounces
        complaints_topic: amazon-ses-complaints

# This will be used for geolocating users, projects, and mail tracking
geolocation:
    # Path to maxmind databases, you need to download (and keep updated) either the free o commercial
    # databases from maxmind. Check this for more info: https://dev.maxmind.com/geoip/geoipupdate/
    # relative paths are allowed (ie: you can save your maxmind databases into config/resources/maxmind if you want)
    # Un comment next 2 lines if you have it installed and working:
    # maxmind:
    #    cities: /usr/share/GeoIP/GeoLite2-City.mmdb

# Social Login Services
# Fill an uncomment the needed one's
oauth:
    # Credentials app Facebook
    facebook:
       active: false
       id:     000000000000000                  #
       secret: aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa #

    # Credentials app Twitter
    twitter:
       active: false
       id:      aaaaaaaaaaaaaaaaaaaaaa                     #
       secret:  aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa #

    # Credentials app Linkedin
    linkedin:
       active: false
       id:     aaaaaaaaaaaa     #
       secret: aaaaaaaaaaaaaaaa #

    # Credentials Google
    google:
       active: false
       id: aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
       secret: aaaaaaaaaaaaaaaaaa

    # OpenID logins does not need for Keys, just active/inactive
    yahoo:
        active: false

    openid:
        active: true


```
