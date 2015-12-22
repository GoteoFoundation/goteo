---
currentMenu: consoledev
---
Console scripts
---------------

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
