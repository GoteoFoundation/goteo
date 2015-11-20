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

use Monolog\Logger;
use Goteo\Util\Monolog\Processor\WebProcessor;

/**
 * Usefull trait to use log on legacy classes
 */
trait StaticLoggerTrait {

    static protected $logger = null;

    static public function setLogger(Logger $logger) {
        static::$logger = $logger;
    }

    static public function getLogger(Logger $logger) {
        return static::$logger;
    }

    static public function log($message, array $context = [], $func = 'info') {
        if(static::$logger) {
            static::$logger->$func($message, WebProcessor::processObject($context));
        }
    }

    static public function debug($txt, array $vars = []) {
        return static::log($txt, $vars, 'debug');
    }

    static public function info($txt, array $vars = []) {
        return static::log($txt, $vars, 'info');
    }

    static public function notice($txt, array $vars = []) {
        return static::log($txt, $vars, 'notice');
    }

    static public function error($txt, array $vars = []) {
        return static::log($txt, $vars, 'error');
    }

    static public function warning($txt, array $vars = []) {
        return static::log($txt, $vars, 'warning');
    }

    static public function critical($txt, array $vars = []) {
        return static::log($txt, $vars, 'critical');
    }

}
