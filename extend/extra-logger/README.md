# Extra Logger Plugin

## This plugin provides extra loggin capabilities for Goteo

Currently only the (Gelf)[https://github.com/bzikarsky/gelf-php] format is supported. This allows to send logs directly to (Graylog)[https://www.graylog.org/]

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

...

```

In order to use the Gelf logger handler you are going to create a new GELF UDP input in your Graylog instance.
