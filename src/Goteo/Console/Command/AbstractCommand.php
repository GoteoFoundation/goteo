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

use Goteo\Console\Console;
use Goteo\Core\Traits\LoggerTrait;
use Goteo\Util\Monolog\Processor\WebProcessor;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCommand extends Command {
    use LoggerTrait;

    protected array $logs = [];
    protected InputInterface $input;
    protected OutputInterface $output;

    public function setInput(InputInterface $input): AbstractCommand
    {
        $this->input = $input;

        return $this;
    }

    public function setOutput(OutputInterface $output): AbstractCommand
    {
        $this->output = $output;

        return $this;
    }

    public function addLogger(Logger $logger, $name = null): AbstractCommand
    {
        if(!$name) $name = $this->getName();
        $this->logs["cli.$name"] = $logger;

        return $this;
    }

    public function getLogger($name = null) {
        if(!$name) $name = $this->getName(); // Name of the command

        return $this->logs["cli.$name"]; // cached instance
    }

    public function log($message, array $context = [], $func = 'info') {
        $logger = $this->getLogger();
        $processed = WebProcessor::processObject($context);
        $context = array_merge(['command' => $this->getName()], $context);
        if(isset($this->output) && isset($this->input)) {
            if(!$this->output->isVerbose()) {
                if($func == 'info') $color = 'green';
                elseif($func == 'notice') $color = 'cyan';
                elseif($func == 'critical') $color = 'red;options=bold';
                elseif($func == 'error') $color = 'red';
                elseif($func == 'warning') $color = 'yellow';
                if($func != 'debug') {
                    $this->output->writeln($color ? "<fg=$color>$message</>" : $message);
                    if($processed) {
                        $this->output->writeln(
                            implode(
                                "\n",
                                array_map(
                                    function ($v, $k) {
                                        return sprintf("\t<fg=blue>%s:</> %s", $k, $v);
                                    },
                                    $processed,
                                    array_keys($processed)
                                )
                            )
                        );
                    }
                }
            }
        }

        if (null !== $logger && method_exists($logger, $func)) {
            return $logger->$func($message, $processed);
        }
    }

    static public function dispatch(string $eventName, Event $event = null): Event
    {
        return Console::dispatch($eventName, $event);
    }

}
