# Extra Logger Plugin

## This plugin provides extra loggin capabilities for Goteo

- STDOUT log directly to the standard output
- GELF format[https://github.com/bzikarsky/gelf-php] format is supported. This allows to send logs directly to (Graylog)[https://www.graylog.org/]

### Configuration:

Configuration must be done in the general `config/settings.yml` Goteo config file.

```yaml
...

plugins:
    extra-logger:
        active: true # activate plugin
        gelf:
            host: your-graylog-host.tld #
            port: 12201
        stdout: color # leave it empty if no stdout logging required, or set it to true if no color needed
...

```

NOTE: 
ENV vars are supported, such as:

```yaml
...

plugins:
    extra-logger:
        active: true # activate plugin
        stdout: '%env(LOG_TO_STDOUT)%'
...

```

In order to use the Gelf logger handler you are going to create a new GELF UDP input in your Graylog instance.
