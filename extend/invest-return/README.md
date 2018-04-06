Invest Recover plugin
==========================


This plugin is meant for disaster recovery.

Provides for a url like `yoursite.com/invest/-some-project-/recover/INVEST_ID`

Where `INVEST_ID` is an entry from the table invest in status "CHARGED" when it should be "CANCELLED" OR "REFUNDED". This can happen if money has been returned outside the platform to the original user and we want that user to easily repeat the invest.

When the user access that URL, then the old invest will be changed to "CANCELLED" and the new one will act as substitute (with same amount, rewards, etc).

**IMPORTANT**

For this to work it affected invests must be configured in `settings.yml` in the plugins zone:

```yaml
...
    invest-return:
        active: true
        invests: [12345,23456,34567] # Affected invests
...
```

IT'S RECOMMENDED TO NOT ACITVATE THIS PLUGIN UNLESS IS NEEDED.
