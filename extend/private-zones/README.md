# PRIVATE ZONES PLUGIN

## This plugin provides basic http authentication for routes

Activating and properly configuring this plugin enables the ability to restrict
the access to some routes (for example /project/my-project) to the defined users
in config.

### Configuration:

Configuration must be done in the general `config/settings.yml` Goteo config file.

```yaml
...

plugins:
    goteo-dev:
        active: false # Dev plugin
    custom-plugin:
        active: true # If I have some custom plugin activated
    private-zones: # PrivateZones plugin
        active: true
        # Configuration options for the plugin goes here
        # public routes (no auth required must be defined as an array)
        # Recomended paths are
        public:
            - /img
            - /json/keepAlive
            - /json/geolocate
        # Private paths must be defined under the "private" key
        # This is a list of users (http), configure paths and passwords as desired
        private:
            everywhere-user:
                password: my-password
                paths:
                    - /
            some-path-user:
                password: my-password
                paths:
                    - /json
                    - /projects/some-project
...

```

### Notes

- Paths are used as a prefix, ie: the `/projects` path will restrict the route `projects/my-project`
