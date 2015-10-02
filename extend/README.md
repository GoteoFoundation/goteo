This folder is for plugins
==========================

Plugins can extend or modify almost any aspect of the site.

Take as example **goteo-dev**

Plugins must be enabled in config/settings.yml, like this:

```yaml
plugins:
    plugin-name:
        active: true
        ... #optional custom configuration directives
```

Directory structure:
--------------------

All plugins must follow this structure (inside `extend` folder):

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


 ### The manifest.yml file:

This file should be like this:

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

