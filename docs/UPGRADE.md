---
currentMenu: upgrade
---
UPGRADE
============

Upgrading from version 2
------------------------

Version 2 is **unsupported** and can be found here:
https://github.com/Goteo/goteo

At this point, everything is experimental.

There are two upgrade SQL scripts:

- `db/upgrade/upgrade-v2-to-v3.sql`
- `db/upgrade/upgrade-from- v3.0e-to-v3.0.1.sql`

Both scripts should be applied in a MySQL console:

```bash
mysql -u your_user -p your_password < db/upgrade/upgrade-v2-to-v3.sql
mysql -u your_user -p your_password < db/upgrade/upgrade-from- v3.0e-to-v3.0.1.sql
```

However, we haven't tested in any production database and very likely errors will be thrown. Debugging info will be appreciated.


