<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    // Define what rule sets will be applied
    $containerConfigurator->import(SymfonySetList::SYMFONY_28);
    $containerConfigurator->import(SymfonySetList::SYMFONY_30);
    $containerConfigurator->import(SymfonySetList::SYMFONY_31);
    $containerConfigurator->import(SymfonySetList::SYMFONY_32);
    $containerConfigurator->import(SymfonySetList::SYMFONY_33);
    $containerConfigurator->import(SymfonySetList::SYMFONY_34);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
};
