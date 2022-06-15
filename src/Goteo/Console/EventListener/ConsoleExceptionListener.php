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

use Bramus\Monolog\Formatter\ColoredLineFormatter;
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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputOption;

/**
 * Inspired in
 * http://php-and-symfony.matthiasnoback.nl/2013/11/symfony2-add-a-global-option-to-console-commands-and-generate-pid-file/
 */
class ConsoleExceptionListener extends AbstractListener
{
	use LockTrait;

    private const GLOBAL_OPTION_LOGMAIL = 'logmail';
    private const GLOBAL_OPTION_LOCK = 'lock';
    private const GLOBAL_OPTION_LOCK_NAME = 'lock-name';

    private $starttime = 0;
    private $mailhandler = null;
    private $debug = false;
    private $lock_name = null;
    private $command = null;

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
            ConsoleEvents::TERMINATE => 'onTerminate',
            ConsoleEvents::ERROR => 'onException',
        ];
    }

    public function log($message, array $context = [], $func = 'info') {
        if($this->command instanceOf AbstractCommand) {
            $this->command->log($message, $context, $func);
        } else {
            parent::log($message, $context, $func);
        }
    }

	public function onCommand(ConsoleCommandEvent $event) {
		$env = Config::get('env');
		$this->starttime = microtime(true);
		$this->command = $command = $event->getCommand();

        $this->addGlobalOptions($command);

        $command->mergeApplicationDefinition();

        $input = new ArgvInput();
        $input->bind($event->getCommand()->getDefinition());
        $name = $command->getName();

        $this->enableVerboseCommand($input);
        $this->enableNiceColorsIfLoggerIsPresent($input, $name);

        $this->debug("Command [".$command->getName()."] started", ['command' => $command->getName(), 'options' => $input->getOptions(), 'started' => $this->starttime]);

		UsersSend::setLogger($this->getLog());

		// Get a lock for this process
		$this->lock_name = $env.'.'.$input->getOption(self::GLOBAL_OPTION_LOCK_NAME);
		$lock = $input->getOption(self::GLOBAL_OPTION_LOCK);
		if (!$this->getNamedLock($this->lock_name)) {
			$this->notice("Command [".$command->getName()."] is still running", ['command' => $command->getName(), self::GLOBAL_OPTION_LOCK => $this->lock_name]);
			// command should terminate if lock option defined
			if ($lock) {
				$this->warning("Aborting execution", ['command' => $command->getName(), self::GLOBAL_OPTION_LOCK => $this->lock_name]);
				$event->disableCommand();
			}
		} elseif ($lock) {
			$this->info("Acquired lock", ['command' => $command->getName(), self::GLOBAL_OPTION_LOCK => $this->lock_name]);
		}

		if ($command instanceOf AbstractCommand) {
			$command->setOutput($event->getOutput());
			$command->setInput($input);
			$command->addLogger($this->getLog());
		}
	}

    private function addGlobalOptions(?Command $command): void
    {
        $this->addGlobalOptionToSendErrorsByEmail($command);
        $this->addGlobalOptionsToLockProcesses($command);
    }

    private function addGlobalOptionToSendErrorsByEmail(?Command $command): void
    {
        $command->addOption(
            self::GLOBAL_OPTION_LOGMAIL,
            null,
            InputOption::VALUE_NONE,
            'Send errors by mail (specified as mail.fail in settings.yml)'
        );
    }

    private function addGlobalOptionsToLockProcesses(?Command $command): void
    {
        $command->addOption(
            self::GLOBAL_OPTION_LOCK,
            null,
            InputOption::VALUE_NONE,
            'Allows only one instance of the process, even in a distributed system (uses MySQL GET_LOCK)'
        );
        $command->addOption(
            self::GLOBAL_OPTION_LOCK_NAME,
            null,
            InputOption::VALUE_OPTIONAL,
            'Specifies the lock name (otherwise will be the command name)',
            $command->getName()
        );
    }

    private function enableVerboseCommand(ArgvInput $input): void
    {
        if ($input->getOption('verbose')) {
            $this->debug = true;
            $stream = new StreamHandler('php://stdout', Logger::DEBUG);
            $logger = App::getService('logger')->pushHandler($stream);
            $logger = App::getService('console_logger');
            $logger->pushHandler($stream);
        }
    }

    /**
     * @throws Config\ConfigException
     */
    private function enableNiceColorsIfLoggerIsPresent(ArgvInput $input, ?string $name): void
    {
        if ($this->getLog()) {
            // if errors should be mailed
            if ($input->getOption(self::GLOBAL_OPTION_LOGMAIL)) {
                $mailer = Mail::createFromHtml(Config::getMail('fail'), '', "CLI-ERROR: [$name] in [" . Config::get('url.main') . "]");
                $this->mailhandler = new MailHandler($mailer, '', Logger::DEBUG, true);
                $this->mailhandler->setFormatter(new HtmlFormatter());
                $this->getLog()->pushHandler(new FingersCrossedHandler($this->mailhandler, Logger::ERROR));
            }

            if (!$input->getOption('no-ansi')) {
                foreach ($this->getLog()->getHandlers() as $handler) {
                    if ($handler instanceof StreamHandler && $handler->getFormatter() instanceof LineFormatter) {
                        $handler->setFormatter(new ColoredLineFormatter());
                    }
                }
            }
        }
    }

	public function onTerminate(ConsoleTerminateEvent $event) {
		$input  = $event->getInput();
		$output = $event->getOutput();

		$this->command = $command = $event->getCommand();

		if ($this->lock_name && $this->releaseNamedLock($this->lock_name)) {
			if ($input->getOption(self::GLOBAL_OPTION_LOCK)) {
				$this->info('Lock released', ['command' => $command->getName(), self::GLOBAL_OPTION_LOCK => $this->lock_name]);
			}
		}

		$now = (microtime(true)-$this->starttime);
		$this->debug("Terminate command", ['command' => $command->getName(), 'options' => $input->getOptions(), 'time' => $now, 'started' => $this->starttime]);
		if ($output->isVerbose()) {
			$output->writeln("Total command time: $now seconds");
		}
		if ($this->mailhandler) {
			$this->mailhandler->sendDelayed();
		}
	}

	public function onException(ConsoleErrorEvent $event) {
		$input  = $event->getInput();
		$output = $event->getOutput();

		$this->command = $command = $event->getCommand();

		$output->writeln(sprintf('Oops, exception thrown while running command <info>%s</info>', $command->getName()));

		$exception = $event->getError();

		$this->error('Command Exception', ['error' => $exception->getMessage(),'command' => $command->getName(), 'options' => $input->getOptions(), 'trace' => ExceptionListener::jTraceEx($exception)]);

		if ($input->getOption(self::GLOBAL_OPTION_LOGMAIL)) {
			$output->writeln(sprintf('<error>Error trace sent to mail %s</error>', Config::get('mail.fail')));
		}
	}
}
