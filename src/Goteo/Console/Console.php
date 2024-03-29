<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console;

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Console {
    static protected $app;
	static protected Application $console;
	static protected array $commands = [];

	/**
	 * Creates a new instance of the App ready to run
	 * Next calls to this method will return the current instantiated App
	 */
	static public function get(): Application
    {
		if (!isset(self::$console)) {
			// Old constants compatibility generated by the dispatcher
			$url = Config::get('url.main');
			define('SITE_URL', (Config::get('ssl')?'https://':'http://').preg_replace('|^(https?:)?//|i', '', $url));

			self::$app = App::getService('console');
            self::$console = new Application();
            self::$console->setDispatcher(self::getConsoleDispatcher());

			foreach (self::$commands as $command) {
				self::$console->add($command);
			}
		}

		return self::$console;
	}

    static private function getConsoleDispatcher(): EventDispatcherInterface
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = App::getService('console_dispatcher');
        return $dispatcher;
    }

	static public function add(Command $command) {
		self::$commands[] = $command;
		if (isset(self::$console)) {
			self::$console->add($command);
		}
	}

	static public function dispatch(string $eventName, Event $event = null): Event
    {
        /** @var Event $event */
        $event = self::getConsoleDispatcher()->dispatch($event, $eventName);
		return $event;
	}

	/**
	 * Enables debug mode witch does:
	 * - *.yml settings always read
	 * - A bottom html profiler tool will be displayed on the bottom of the page
	 * - SQL queries will be collected fo statistics
	 * - Html/php error will be shown
	 */
	static public function debug(bool $enable = null): bool
    {
		return App::debug($enable);
	}
}
