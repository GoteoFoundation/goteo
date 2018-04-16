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

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\EventListener\ExceptionListener;
use Goteo\Console\Command\AbstractCommand;
use Goteo\Console\UsersSend;
use Goteo\Core\Traits\LockTrait;
use Goteo\Model\Mail;
use Goteo\Util\Monolog\Handler\MailHandler;
use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;

use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

/**
 * Inspired in
 * http://php-and-symfony.matthiasnoback.nl/2013/11/symfony2-add-a-global-option-to-console-commands-and-generate-pid-file/
 */

class ConsoleExceptionListener extends AbstractListener {
	use LockTrait;

    private $starttime   = 0;
    private $mailhandler = null;
    private $debug       = false;
    private $lock_name   = null;
    private $command   = null;

    public function log($message, array $context = [], $func = 'info') {
        if($this->command instanceOf AbstractCommand) {
            $this->command->log($message, $context, $func);
        }
        else {
            parent::log($message, $context, $func);
        }
    }

	public function onCommand(ConsoleCommandEvent $event) {
		$env             = Config::get('env');
		$this->starttime = microtime(true);

		// get the command to be executed
		$this->command = $command = $event->getCommand();


        // add a global option for sending errors by mail to the command
        $command->addOption('logmail', null, InputOption::VALUE_NONE, 'Send errors by mail (specified as mail.fail in settings.yml)');

        // add a global option for locking processes to the command
        $command->addOption('lock', null, InputOption::VALUE_NONE, 'Allows only one instance of the process, even in a distributed system (uses MySQL GET_LOCK)');
        $command->addOption('lock-name', null, InputOption::VALUE_OPTIONAL, 'Specifies the lock name (otherwise will be the command name)', $command->getName());

        // merge the application's input definition
        $command->mergeApplicationDefinition();

        // get a new input argument
        $input = new ArgvInput();

        // we use the input definition of the command
        $input->bind($event->getCommand()->getDefinition());

        $name = $command->getName();

		// If verbose, debut to stderr
		if ($input->getOption('verbose')) {
			$this->debug = true;

            // Add a log level debug to stderr in the App general log
            $stream = new StreamHandler('php://stdout', Logger::DEBUG);
            $logger = App::getService('logger')->pushHandler($stream);

            $logger = App::getService('console_logger');

            $logger->pushHandler($stream);
		}

		// nice colors
		if ($this->getLog()) {
			// if errors should be mailed
			if ($input->getOption('logmail')) {
				$mailer            = Mail::createFromHtml(Config::getMail('fail'), '', "CLI-ERROR: [$name] in [" .Config::get('url.main')."]");
				$this->mailhandler = new MailHandler($mailer, '', Logger::DEBUG, true);
				$this->mailhandler->setFormatter(new HtmlFormatter());
				$this->getLog()->pushHandler(new FingersCrossedHandler($this->mailhandler, Logger::ERROR));
			}

			if (!$input->getOption('no-ansi')) {
				foreach ($this->getLog()->getHandlers() as $handler) {
					if ($handler instanceOf StreamHandler && $handler->getFormatter() instanceOf LineFormatter) {
						$handler->setFormatter(new ColoredLineFormatter());
					}
				}
			}
		}

		$this->debug("Command [".$command->getName()."] started", ['command' => $command->getName(), 'options' => $input->getOptions(), 'started' => $this->starttime]);

		// Add logger for some Objects
		UsersSend::setLogger($this->getLog());

		// Get a lock for this process
		$this->lock_name = $env.'.'.$input->getOption('lock-name');
		$lock            = $input->getOption('lock');
		if (!$this->getNamedLock($this->lock_name)) {
			$this->notice("Command [".$command->getName()."] is still running", ['command' => $command->getName(), 'lock' => $this->lock_name]);
			// command should terminate if lock option defined
			if ($lock) {
				$this->warning("Aborting execution", ['command' => $command->getName(), 'lock' => $this->lock_name]);
				$event->disableCommand();
			}
		} elseif ($lock) {
			$this->info("Acquired lock", ['command' => $command->getName(), 'lock' => $this->lock_name]);
		}

		if ($command instanceOf AbstractCommand) {
			// Replace input/output
			$command->setOutput($event->getOutput());
			$command->setInput($input);
			// Add logger
			$command->addLogger($this->getLog());
		}

	}

	public function onTerminate(ConsoleTerminateEvent $event) {
		// get the input/output
		$input  = $event->getInput();
		$output = $event->getOutput();

		// get the command that has been executed
		$this->command = $command = $event->getCommand();

		if ($this->lock_name && $this->releaseNamedLock($this->lock_name)) {
			if ($input->getOption('lock')) {
				$this->info('Lock released', ['command' => $command->getName(), 'lock' => $this->lock_name]);
			}
		}

		$now = (microtime(true)-$this->starttime);
		$this->debug("Terminate command", ['command' => $command->getName(), 'options' => $input->getOptions(), 'time' => $now, 'started' => $this->starttime]);
		if ($output->isVerbose()) {
			$output->writeln("Total command time: $now seconds");
		}
		// Sent delayed emails
		if ($this->mailhandler) {
			$this->mailhandler->sendDelayed();
		}
		// // change the exit code
		// $event->setExitCode(128);
	}

	public function onException(ConsoleExceptionEvent $event) {
		$input  = $event->getInput();
		$output = $event->getOutput();

		$this->command = $command = $event->getCommand();

		$output->writeln(sprintf('Oops, exception thrown while running command <info>%s</info>', $command->getName()));

		// get the current exit code (the exception code or the exit code set by a ConsoleEvents::TERMINATE event)
		$exitCode  = $event->getExitCode();
		$exception = $event->getException();

		$this->error('Command Exception', ['error' => $exception->getMessage(),'command' => $command->getName(), 'options' => $input->getOptions(), 'trace' => ExceptionListener::jTraceEx($exception)]);

		if ($input->getOption('logmail')) {
			$output->writeln(sprintf('<error>Error trace sent to mail %s</error>', Config::get('mail.fail')));
		}
		// change the exception to another one and show some info about it
		// $event->setException(new \LogicException(ExceptionListener::jTraceEx($exception), $exitCode, $exception));
	}

	public static function getSubscribedEvents() {
		return array(
			ConsoleEvents::COMMAND   => 'onCommand',
			ConsoleEvents::TERMINATE => 'onTerminate',
			ConsoleEvents::EXCEPTION => 'onException',
		);
	}
}
