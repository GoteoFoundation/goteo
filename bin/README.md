Goteo scripts
=============

Part of the behaviour of Goteo must be achieved by the use of console comands.
These commands processes end-round termination project status changes, refunding payments for failed processes, etc.

All commmands uses the [Symfony Console component](http://symfony.com/doc/current/components/console/introduction.html)

Should be run in a terminal with the PHP cli:
```bash
php bin/console
```

Or, if bin/console is executable, simply type (in the goteo project folder):

```bash
./bin/console
```

Last commands should show you a list of available commands.

Any command has his help message:

```bash
./bin/console test --help
```


Mandatory Cron commands:
------------------------

1. TODO

Commands:
------------------

1. `./bin/console round` in_process
1. `./bin/console refund` done
1. `./bin/console failed` todo


Developers:
-----------

Check the TestCommand as an example:
`src/Goteo/Command/TestCommand.php`
