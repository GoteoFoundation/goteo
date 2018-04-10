<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Routing\Route;


Lang::addYamlTranslation('es', __DIR__ .'/Translations/es.yml', 'generic');
Lang::addYamlTranslation('ca', __DIR__ .'/Translations/ca.yml', 'generic');
Lang::addYamlTranslation('en', __DIR__ .'/Translations/en.yml', 'generic');

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ .'/src');

// Adding custom services to the service container:
$sc = App::getServiceContainer();

// Invest add Fiscal info listener
$sc->register('goteo.listener.invest_return', 'Goteo\EventListener\InvestStatusChangerListener')
   ->setArguments(array(new Reference('paylogger')));

$sc->getDefinition('dispatcher')
   ->addMethodCall('addSubscriber', array(new Reference('goteo.listener.invest_return')));

$routes = App::getRoutes();

$routes->add('invest-select-payment-recover', new Route(
    '/invest/{project_id}/recover/{id}',
    array('_controller' => 'Goteo\Controller\InvestRecoverController::recoverAction',
        )
));
