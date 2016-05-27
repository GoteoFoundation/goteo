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


## Dev plugin

There's a demo plugin with some utilities distributed with the code, the  `goteo-dev` plugin.
It adds a toolbar at the bottom of the page with debugging info.
Also, it provides a useful command line that can be used to create testing situations:

```bash
./bin/console dev:statusinit -h
```

```
Help:
 This script initializes projects, invests and users to some known status in order to allow tests
 
 Usage:
 
 Run the SQL scripts to initialize status
 ./bin/console dev:statusinit
 
 Run the SQL scripts to initialize status with published, passed and closed dates
 so it can be used to test the endround (and others such as projectwatch) command
 
 One day succeeded, published or failed projects:
 ./bin/console dev:statusinit --delta 1
 
 Ten days succeeded, published or failed projects:
 ./bin/console dev:statusinit --delta 10
 
 Removes all testing data:
 ./bin/console dev:statusinit --erase
 
 Removes all testing data and creates a fresh one:
 ./bin/console dev:statusinit -ec
 
 Removes all testing data and creates a fresh one 5 days in the past:
 ./bin/console dev:statusinit -ecd 5
 
 Be verbose (show the SQL executed):
 ./bin/console dev:statusinit --erase -v

```
