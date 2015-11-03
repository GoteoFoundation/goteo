<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Goteo\Util\Monolog\Handler\MailHandler;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\IntrospectionProcessor;

use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Model\Mail;

abstract class AbstractCommand extends Command {
    protected $logs = [];

    /**
     * Retrieves a instance of a Logger
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function getLog($name = null) {
        if(!$name) $name = $this->getName(); // Name of the command

        if(isset($this->logs[$name])) return $this->logs[$name]; // cached instance

        $env = Config::get('env');
        return $this->logs[$name] =
                    new Logger($name, [
                        new StreamHandler('php://stderr', Logger::INFO),
                        new RotatingFileHandler(GOTEO_LOG_PATH . "$name-$env.log", 0, Logger::DEBUG),
                        new MailHandler(Mail::createFromHtml(Config::getMail('fail'),
                                                             '',
                                                             "CLI-ERROR: [$name] in [" . Config::get('url.main') . "] ",
                                                             "<pre>SERVER: " . print_R($_SERVER, 1) . "</pre>\n"
                                                             ),
                                        Logger::ERROR)
                    ], [
                        // processor, memory usage
                        new MemoryUsageProcessor,
                        new IntrospectionProcessor(Logger::ERROR)
                    ]);
    }

    /**
     * Logs info to the default log
     * @return [type] [description]
     */
    public function info($txt) {
        return $this->getLog()->info($txt);
    }

    /**
     * Logs warnings to the default log
     * @return [type] [description]
     */
    public function warn($txt) {
        return $this->getLog()->warn($txt);
    }

    /**
     * Logs errors to the default log
     * @return [type] [description]
     */
    public function error($txt) {
        return $this->getLog()->error($txt);
    }

    /**
     * Logs debug to the default log
     * @return [type] [description]
     */
    public function debug($txt) {
        return $this->getLog()->debug($txt);
    }

    /**
     * Dispatchs an event
     * Events can be handled by any suscriber
     * @param  string     $eventName event ID
     * @param  Event|null $event     Event object
     * @return Event                 the result object
     */
    static public function dispatch($eventName, Event $event = null) {
        return App::dispatch($eventName, $event);
    }

}
