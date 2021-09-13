<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Monolog;

use Goteo\Application\Config;
use RuntimeException;

class Logger extends \Monolog\Logger {
    public function addRecord($level, $message, array $context = array())
    {
        if(Config::get('debug')) {
            parent::addRecord($level, $message, $context);
        } else {
            try {
                parent::addRecord($level, $message, $context);
            } catch(RuntimeException $e) {
                // if not in debug mode, failure to process some logs is ignored
                if(strpos($e->getFile(), "/Gelf/") === false) {
                    throw $e;
                }
            }
        }
    }
}