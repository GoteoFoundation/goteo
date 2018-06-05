# CUSTOM DOMAINS PLUGIN

## This plugin provides custom domains and redirections for Goteo

Activating and properly configuring this plugin enables the ability to show the content of any path defined as it was an independent webpage with his own domain (or subdomain).

For example http://example.com/project/my-project could be configured to be accessed with a custom domain: http://my-project.org/ or a custom subdomains http://my-project.example.com/


## Configuration:

### Custom domains

Configuration must be done in the general `config/settings.yml` Goteo config file.

Multiple path can be specified for any domain

The first path is the one treated as index (will convert the path to "/")

Next paths will be appended to the custom domain as they are in the main domain

> **NOTES:** 
> - DNS domains/subdomains configured in this plugin must be configured in the DNS provider to point to the main Installation url.
> - Paths are treated as prefixes, any following url starting with the same path will use the custom domain as well
> - DO NOT PUT http:// or https:// in custom domain blocks
> - There's no automatic treatment of the session between different domains (although they should work in subdomains), so the user session won't be conserved.


### Custom redirections

This plugin allows also to define arbitrary redirections that may be useful in some cases. They are just a list of an origin => destination.

> **NOTES**
> - Redirects works based on prefix
> - Order is important, the first matching prefix will be used and the rest ignored


### Settings example

```yaml
...

plugins:
    goteo-dev:
        active: false # Dev plugin

    custom-plugin:
        active: true # If I have some custom plugin activated...

    custom-domins: # Custom Domains plugin
        active: true
        # Define custom domains here
        domains:
            blog.example.com:
                - /blog
            nice-project.org:
                - /project/ugly-project
            search.example.com:
                - /discover
        # Define custom redirects here
        redirects:
            direct.example.com/p: http://example.com/project/some-project
            blog.example.com/p: http://blog.example.com/

...

```

