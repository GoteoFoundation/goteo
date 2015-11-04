<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Monolog\Logger;

use Goteo\Console\Application as Console;

abstract class AbstractCommand extends Command {
    protected $logs = [];

    public function addLogger(Logger $logger) {
        $this->logs[$logger->getName()] = $logger;
        return $this;
    }

    /**
     * Retrieves a instance of a Logger
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function getLogger($name = null) {
        if(!$name) $name = $this->getName(); // Name of the command

        return $this->logs["cli.$name"]; // cached instance
    }

    /**
     * Logs info to the default log
     * @return [type] [description]
     */
    public function info($txt) {
        return $this->getLogger()->info($txt);
    }

    /**
     * Logs warnings to the default log
     * @return [type] [description]
     */
    public function warn($txt) {
        return $this->getLogger()->warn($txt);
    }

    /**
     * Logs errors to the default log
     * @return [type] [description]
     */
    public function error($txt) {
        return $this->getLogger()->error($txt);
    }

    /**
     * Logs debug to the default log
     * @return [type] [description]
     */
    public function debug($txt) {
        return $this->getLogger()->debug($txt);
    }

    /**
     * Dispatchs an event
     * Events can be handled by any suscriber
     * @param  string     $eventName event ID
     * @param  Event|null $event     Event object
     * @return Event                 the result object
     */
    static public function dispatch($eventName, Event $event = null) {
        return Console::dispatch($eventName, $event);
    }

}
