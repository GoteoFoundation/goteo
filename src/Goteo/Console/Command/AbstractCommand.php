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
use Goteo\Core\Traits\LoggerTrait;

abstract class AbstractCommand extends Command {
    use LoggerTrait;

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
        $processed = WebProcessor::processObject($context);
        $context = array_merge(['command' => $this->getName()], $context);
        if($this->output && $this->input) {
            if(!$this->output->isVerbose()) {
                if($func == 'info') $color = 'green';
                elseif($func == 'notice') $color = 'cyan';
                elseif($func == 'critical') $color = 'red;options=bold';
                elseif($func == 'error') $color = 'red';
                elseif($func == 'warning') $color = 'yellow';
                if($func != 'debug') {
                    $this->output->writeln($color ? "<fg=$color>$message</>" : $message);
                    if($processed) $this->output->writeln(implode("\n", array_map(function ($v, $k) { return sprintf("\t<fg=blue>%s:</> %s", $k, $v); }, $processed, array_keys($processed))));
                }
            }
        }

        if (null !== $logger && method_exists($logger, $func)) {
            return $logger->$func($message, $processed);
        }
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
