<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArgvInput;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Goteo\Util\Monolog\Handler\MailHandler;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\IntrospectionProcessor;

use Goteo\Application\Config;
use Goteo\Application\EventListener\ExceptionListener;
use Goteo\Console\Command\AbstractCommand;
use Goteo\Model\Mail;

//
class ConsoleExceptionListener implements EventSubscriberInterface
{
    private $_mailhandler = null;

    public function onCommand(ConsoleCommandEvent $event) {
        // get the command to be executed
        $command = $event->getCommand();

        // add a global option to the command
        $command->addOption('logmail', null, InputOption::VALUE_NONE, 'Send errors by mail (specified in settings.yml)');
        // merge the application's input definition
        $command->mergeApplicationDefinition();

        // get a new input argument
        $input = new ArgvInput();

        // we use the input definition of the command
        $input->bind($event->getCommand()->getDefinition());

        $name = $command->getName();
        // if mail errors
        if($input->getOption('logmail')) {
            $this->_mailhandler = new MailHandler(Mail::createFromHtml(Config::getMail('fail'),
                                                             '',
                                                             "CLI-ERROR: [$name] in [" . Config::get('url.main') . "] ",
                                                             "<pre>SERVER: " . print_R($_SERVER, 1) . "</pre>\n"
                                                             ),
                                        Logger::ERROR);
        }

        $env = Config::get('env');

        if(!$command instanceOf AbstractCommand) {
            return;
        }

        // Add logger
        $command->addLogger(
                    new Logger("cli.$name", [
                        new StreamHandler('php://stderr', Logger::INFO),
                        new RotatingFileHandler(GOTEO_LOG_PATH . "cli.$name-$env.log", 0, Logger::DEBUG),
                        $this->_mailhandler
                    ], [
                        // processor, memory usage
                        new MemoryUsageProcessor,
                        new IntrospectionProcessor(Logger::ERROR)
                    ])
                );
    }

    public function onTerminate(ConsoleTerminateEvent $event) {
        // // get the output
        // $output = $event->getOutput();

        // // get the command that has been executed
        // $command = $event->getCommand();

        // // // display something
        // // $output->writeln(sprintf('After running command <info>%s</info>', $command->getName()));

        // // change the exit code
        // $event->setExitCode(128);
    }

    public function onException(ConsoleExceptionEvent $event) {
        $output = $event->getOutput();

        $command = $event->getCommand();

        $output->writeln(sprintf('Oops, exception thrown while running command <info>%s</info>', $command->getName()));

        // get the current exit code (the exception code or the exit code set by a ConsoleEvents::TERMINATE event)
        $exitCode = $event->getExitCode();
        $exception = $event->getException();

        if($command instanceOf AbstractCommand) {
            // $command->error($exception->getMessage());
            foreach(explode("\n", ExceptionListener::jTraceEx($exception)) as $line) {
                $command->error($line);
            }
        }
        // change the exception to another one and show some info about it
        // $event->setException(new \LogicException(ExceptionListener::jTraceEx($exception), $exitCode, $exception));
    }

    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::COMMAND => 'onCommand',
            ConsoleEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::EXCEPTION => 'onException',
        );
    }
}

