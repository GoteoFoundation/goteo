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

Goteo system needs to execute some periodic tasks in order to keep the projects funding status up to date. Other task involve sending mails to owners and investors (backers) and database cleaning.

Goteo Commands:
------------------

1. `./bin/console endround` This is the most important command of all. Should be run by cron process once a day in order to check and change projects status, process refunds (if needed) among other stuff (like sending mails to owner or backers).
    This command triggers `./bin/console refund` command to process refunds for failed projects.
    Running this command without the `--update` options will not make any changes.
    Run `./bin/console endround --help` for options and description

2. `./bin/console refund` This command allows to process refund invests manually or authomatically. 
   Running this command without the `--update` options will not make any changes.
   Run `./bin/console refund --help` for options and description

3. `./bin/console cron` This programs just runs other commands based on a schedule.    
   Schedule table is in `Resources/crontab.yml`
   This command should be placed in a cron management system with the `--lock` option activated:
   **Cron line suggested example**
   `* * * * *   www-data    nice /usr/bin/php /path/to/installation/bin/console cron --lock --logmail > /dev/null 2>&1`

4. `./bin/console mailing` This program processes pending massive mailing. This makes use of the concurrent capabilites [http://symfony.com/doc/current/components/process.html](Symfony process commponent) so be aware that you'll need (http://php.net/manual/es/function.proc-open.php)[proc_open()] function active in your PHP installation.

5. `./bin/console sendmail` This program sends and individual mail from the massive mailing list. Althoug it can be used manually for testing, it's bassically used by the `mailing` task to make concurrent sending.

6. `./bin/console projectwatch` This command sends advises to the project's owners depending on the status of of the project (advises base on template mails).
   **Note that this command is old and needs to be refactored (You cannont execute this command in read-only mode)**

7. `./bin/console dbverify` Another old command that needs refactoring. Cleans some old databases entries.  
   **Note that this command is old and needs to be refactored (You cannont execute this command in read-only mode)**

Global options
--------------

Some options are common for all commands:

1. `--help` displays help about the command. 
   Example: `./bin/console endround --help`

2. `--verbose` Displays on the standard console output (like `echo` command) the generated log
   Example: `./bin/console endround --verbose`

3. `--logmail` Emails the full log if a ERROR level is triggered.
   Email should be defined into `settings.yml` -> `mail.fail`
   Example: `./bin/console endround --logmail`

4. `--lock` Locks the process by using the `GET_LOCK` function on MySQL server.
   This way the running process can be used in a replicated distributed cron system
   ensuring that only one instance of the process will be running. 
   **Note**: Command called without this option may be allowed to execute even if some lock exists 
   Example: `./bin/console endround --lock --logmail`

Developers:
-----------

**Dev commands**:

`./bin/console dev:statusinit` if you have the `extend/goteo-dev` plugin activated this command allows to create some projects to test the endround process.
Use `./bin/console dev:statusinit --clean` to delete all created data


Check the TestCommand as an example of how to create a command:
`src/Goteo/Command/TestCommand.php`

Commands can be added to the console system in the `extend/plugin-name/start.php` such as:

```php
use Symfony\Component\DependencyInjection\Reference;
use Goteo\Console\Console;
use Goteo\Application\App;
use Goteo\Console\Command\CronCommand;

// Console add command
Console::add(new MyPlugin\Command\MyPluginCommand());

// If needed, you can add your own listeners to handle ConsoleEvents
// this will allow you to add some extra functionality to some actions
// 
// See file src/Console/ConsoleEvents.php 
// and url http://symfony.com/doc/current/components/console/events.html
// for more information

// Adding custom services to the service container:
$sc = App::getServiceContainer();
// Take advantage of the general service container
// Adding new listener for some actions
$sc->register('console.myplugin.mylistener', 
              'Goteo\Console\EventListener\ConsoleMyPluginAddonListener')
   ->setArguments([new Reference('console_logger')]);
$sc->getDefinition('console_dispatcher')
   ->addMethodCall('addSubscriber', [new Reference('console.myplugin.mylistener')]);

// Cron jobs can be added directly to the main CronCommand task if needed:
CronCommand::addCrontabLine([
    'schedule' => '25 ' . (Config::get('env') == 'real' ? 5 : 6) . ' * * *',
    'command' => 'bin/console myplugin:mycommand --logmail --lock > /dev/null',
    'type' => 'php',
    'nice' => true
]);
```
