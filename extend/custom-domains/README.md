# CUSTOM DOMAINS PLUGIN

## This plugin provides basic http authentication for routes

Activating and properly configuring this plugin enables the ability to redirect (if needed) some paths of Goteo to custom Domains.


For example http://goteo.org/project/my-project could be configured to be accessed with a custom domain: http://my-project.org/ or a custom subdomains http://my-project.goteo.org/

> **NOTE:** DNS domains/subdomains configured in this plugin must be configured in the DNS provider to point to the main Goteo url.


### Configuration:

Configuration must be done in the general `config/settings.yml` Goteo config file.

```yaml
...

plugins:
    goteo-dev:
        active: false # Dev plugin
    custom-plugin:
        active: true # If I have some custom plugin activated
    custom-urls: # PrivateZones plugin
        active: true
        # Configuration options for the plugin goes here
...

```

