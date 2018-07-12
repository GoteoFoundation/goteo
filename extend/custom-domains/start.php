<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\DependencyInjection\Reference;

// Autoload additional Classes
Config::addAutoloadDir(__DIR__ .'/src');

// Adding custom services to the service container:
$sc = App::getServiceContainer();

// Custom domain listener reference
$sc->register('custom-domains.domain_listener', 'CustomDomains\EventListener\DomainListener')
   ->setArguments(array(new Reference('logger'))); // 'logger' is the default logger defined in the main container.php file


// Add the subscriber to the service container
$sc->getDefinition('dispatcher')
   ->addMethodCall('addSubscriber', array(new Reference('custom-domains.domain_listener')))
;

