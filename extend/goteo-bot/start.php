<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\DependencyInjection\Reference;

use GoteoBot\Model\Bot\TelegramBot;

// Autoload additional Classes (if necessary)
Config::addAutoloadDir(__DIR__ .'/src');

// Adding lang files (if necessary)
foreach (['bot'] as $group) {
	foreach (Lang::listAll('name', false) as $lang => $name) {
		Lang::addYamlTranslation($lang, __DIR__ .'/Resources/translations/'.$lang.'/'.$group.'.yml');
	}
}

// Adding custom services to the service container:
$sc = App::getServiceContainer();
$sc->register('goteo.listener.controller_sidemenu', 'GoteoBot\Application\EventListener\BotControllerListener')
	->setArguments(array(new Reference('logger')));
	
$sc->getDefinition('dispatcher')
	->addMethodCall('addSubscriber', array(new Reference('goteo.listener.controller_sidemenu')));
	


// Adding Routes:

$routes = App::getRoutes();

$routes->add('dashboard-project-integrations', new Route(
  'dashboard/project/{pid}/integration',
  array('_controller' => 'GoteoBot\Controller\BotProjectDashboardController::integrationAction')
));

// Set up the webhooks for the bots
$routes->add('goteobot-telegram-webhook', new Route(
		'/goteobot/api/telegram/{token}',
		array('_controller' => 'GoteoBot\Controller\Api\BotApiController::getUpdate')
));

$bot = new TelegramBot();
$bot->createBot();
$bot->setWebhook();

