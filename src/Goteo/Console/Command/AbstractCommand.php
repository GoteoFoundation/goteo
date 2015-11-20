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
use Symfony\Component\EventDispatcher\Event;

use Monolog\Logger;

use Goteo\Console\Console;
use Goteo\Util\Monolog\Processor\WebProcessor;

abstract class AbstractCommand extends Command {
    protected $logs = [];
    protected $input;
    protected $output;

    public function setInput(InputInterface $input) {
        $this->input = $input;
        return $this;
    }

    public function setOutput(OutputInterface $output) {
        $this->output = $output;
        return $this;
    }

    public function addLogger(Logger $logger, $name = null) {
        if(!$name) $name = $this->getName(); // Name of the command
        $this->logs["cli.$name"] = $logger;
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
     */
    public function log($message, array $context = [], $func = 'info') {
        $logger = $this->getLogger();
        $context = array_merge(['command' => $this->getName()], $context);
        if($this->output && $this->input) {
            if($this->input->getOption('verbose')) {
                $this->output->writeln($txt);
            }
        }

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
