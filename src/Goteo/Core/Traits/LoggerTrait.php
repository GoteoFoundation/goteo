<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Core\Traits;

use Goteo\Application\App;
use Goteo\Util\Monolog\Processor\WebProcessor;

/**
 * Trait to add log common methods
 */
trait LoggerTrait {

    public function log($message, array $context = [], $func = 'info') {
        $logger = App::getService('logger');
        if (null !== $logger && method_exists($logger, $func)) {
            return $logger->$func($message, WebProcessor::processObject($context));
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
}
