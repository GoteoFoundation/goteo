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

abstract class AbstractListener implements EventSubscriberInterface {
    protected $logger;

    public function __construct(LoggerInterface $logger = null) {
        $this->logger = $logger;
    }

    public function log($message, array $context = [], $func = 'info') {
        if (null !== $this->logger && method_exists($this->logger, $func)) {
            return $this->logger->$func($message, WebProcessor::processObject($context));
        }
    }
    public function info($message, array $context = []) {
        return $this->log($message, $context, 'info');
    }

    public function error($message, array $context = []) {
        return $this->log($message, $context, 'error');
    }

    public function warning($message, array $context = []) {
        return $this->log($message, $context, 'warning');
    }

    public function notice($message, array $context = []) {
        return $this->log($message, $context, 'notice');
    }

    public function critical($message, array $context = []) {
        return $this->log($message, $context, 'critical');
    }

    public function debug($message, array $context = []) {
        return $this->log($message, $context, 'debug');
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
