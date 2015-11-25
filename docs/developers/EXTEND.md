---
currentMenu: extend
---
Extending Goteo
===============

Plugins can extend or modify almost any aspect of the site.

The plugins' folder
-------------------

Take as an example **goteo-dev**

Plugins must be enabled in config/settings.yml, like this:

```yaml
plugins:
    plugin-name:
        active: true
        ... #optional custom configuration directives
```

### Directory structure:

All plugins must follow this structure (inside the `extend` folder):

```
extend/
└── plugin-name
    ├── start.php         <- Mandatory, Will be called on app init  
    ├── manifest.yml      <- Mandatory, Should specify name, version
    ├── README.md         <- Optional
    │── src/
    │   └── Vendor/
    │       └── Foo/
    │           └── Bar.php
    │
    └── Resources/       <- Optional
        ├── templates/   <- Optional, templates will be searched here first
        ...

```


### The `manifest.yml` file:

This file should follow this structure:

```yaml
# ...Plugin description...
---

name: PluginName
version: 1.0
# Next key/value array will be used by Grunt to copy this files under the "dist/" directory
assets:
    # Will be copied as dist/plugin-name/assets
    Resources/assets: plugin-name/assets
    ...
```


### The `start.php` file:

This will be fully documented some day. 

Meanwhile, please take a look at the file  `extend/goteo-dev/start.php`  as an example.
