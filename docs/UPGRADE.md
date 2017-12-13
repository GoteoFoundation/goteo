---
currentMenu: upgrade
---
UPGRADE
============

Upgrading in versions bigger than **v3.2**
------------------------------------------

Anytime the Goteo code has been update you just need to run the comman `migrate`:

**Shows if the systems needs some SQL upgrade**:

```
php bin/console migrate
```

**Executes the upgrade, if needed**:

```
php bin/console migrate all
```


Older versions
==============

> Use this instructions only as a reference if you already have a copy of goteo less than **v3.2**
>
> To install Goteo from scratch, read [INSTALL](install.html)
>
> This instructions will bring you up to the **v3.2** version and, from there, you just keep updating using the `./bin/console migrate` tool.


Upgrading from version 2
------------------------

Version 2 is **unsupported** and can be found here:
https://github.com/Goteo/goteo

We made un script to upgrade from **v2** to **v3.0**:

```
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-v2-to-v3.sql
```

Apply it and then execute the next step to upgrade to **v3.2**


Upgrading form any version from **3.0** to **3.2**
------------------------------------------

Needless to say that you MUST have backups in case things go wrong.

The folder `db/upgrade` contains several sql script tha must be applied in the correct order:

Apply these scripts by using the MySQL console (you may apply only the needed ones depending on your version):

```bash
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0e-to-v3.0.1.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.1-to-v3.0.2.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.2-to-v3.0.3.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.4-to-v3.0.5.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.5-to-v3.0.6.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.6-to-v3.0.7.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.7-to-v3.0.9.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.0.9-to-v3.1.sql
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.1-to-v3.2.sql
```



Other upgrades (up to v3.2 only)
--------------------------------

You better review the code of this SQL instructions before proceed

Apply this script to import the new milestones:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-milestones.sql
```

Apply this script to import some new templates:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-templates.sql
```

Apply this script to recreate the pages structure:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-pages.sql
```



Old (manual) install method up to **v3.2**
------------------------------------------


- `db/install/structure.sql`
- `db/install/upgrade-to-v3.1.sql`
- `db/install/data.sql`
- `db/install/templates.sql`


Both scripts should be applied in a MySQL console:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/install/structure.sql
```

Then, apply the minor-version update:

```bash
mysql -u your_user -pyour_password your_goteo_db < db/install/upgrade-to-v3.1.sql
```

And, finally, import some bare data (glossary and faq may be optional, or you can delete date afterwards):

```bash
mysql -u your_user -pyour_password your_goteo_db < db/install/data.sql
mysql -u your_user -pyour_password your_goteo_db < db/install/templates.sql
mysql -u your_user -pyour_password your_goteo_db < db/install/glossary.sql
mysql -u your_user -pyour_password your_goteo_db < db/install/faq.sql
```

Upgrade to v3.2

```
mysql -u your_user -pyour_password your_goteo_db < db/upgrade/upgrade-from-v3.1-to-v3.2.sql
```

