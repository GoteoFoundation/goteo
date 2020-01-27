# BOT PLUGIN

## This plugin provides implementations of some bot providers

This plugin has a Bot interface and implementations of some bots.

Currently the bots that are included are:

- Telegram

For this bots we use external libraries that are:

- [unreal4u/telegram-api](https://github.com/unreal4u/telegram-api)

### Configuration:

Configuration must be done in the general `config/settings.yml` Goteo config file.

```yaml
...

plugins:
    goteo-bot:
        active: true # Bot plugin
...

bot:
  telegram:
    token:

```

### Notes

- To use this plugin you have to add your telegram token. 

