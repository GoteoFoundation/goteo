<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

use Goteo\Application\App;
use Goteo\Util\Monolog\Processor\WebProcessor;
use Goteo\Core\Traits\LoggerTrait;

abstract class AbstractListener implements EventSubscriberInterface {
    use LoggerTrait;

    public function __construct(LoggerInterface $logger = null) {
        $this->setLog($logger);
    }

    /**
     * Handy method to get the service container object
     */
    public function getContainer() {
        return App::getServiceContainer();
    }

    /**
     * Handy method to get the getService function
     */
    public function getService($service) {
        return App::getService($service);
    }

}
