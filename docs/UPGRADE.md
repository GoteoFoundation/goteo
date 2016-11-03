---
currentMenu: upgrade
---
UPGRADE
============

Upgrading from version 2
------------------------

Version 2 is **unsupported** and can be found here:
https://github.com/Goteo/goteo

At this point, all of this is quite experimental, use it at your own risk.
Needless to say that you MUST have backups in case things go wrong.

The folder `db/upgrade` contains several sql script tha must be applied in the correct order:

Apply these scripts by using the MySQL console (you may apply only the needed ones depending on your version):

```bash
mysql -u your_user -p your_password < db/upgrade/upgrade-v2-to-v3.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from-v3.0e-to-v3.0.1.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from-v3.0.1-to-v3.0.2.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from-v3.0.2-to-v3.0.3.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from-v3.0.4-to-v3.0.5.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from-v3.0.5-to-v3.0.6.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from-v3.0.6-to-v3.0.7.sql
```

However, we haven't tested it in any production database and very likely errors will be thrown.
Debugging info will be appreciated.


Other upgrades
--------------

Apply this script to import the new milestones:

```bash
mysql -u your_user -p your_password < db/upgrade/upgrade-milestones.sql
```

Apply this script to import the new templates:

```bash
mysql -u your_user -p your_password < db/upgrade/upgrade-templates.sql
```

Apply this script to recreate the new pages structure:

```bash
mysql -u your_user -p your_password < db/upgrade/upgrade-pages.sql
```
