<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\GelfHandler;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;
use Bramus\Monolog\Formatter\ColoredLineFormatter;

$stdout = Config::get('plugins.extra-logger.stdout');
if($stdout) {
    $handler = new StreamHandler('php://stdout', Logger::DEBUG);
    if($stdout == 'color') {
        $handler->setFormatter(new ColoredLineFormatter());
    }
    // Add a log level debug to stderr
    App::getService('logger')->pushHandler($handler);
    App::getService('syslogger')->pushHandler($handler);
    App::getService('paylogger')->pushHandler($handler);
}

$gelf = Config::get('plugins.extra-logger.gelf');
if($gelf && $gelf["host"]) {
    $handler = new GelfHandler(new Publisher( new UdpTransport($gelf["host"], $gelf["port"]) ));

    App::getService('logger')->pushHandler($handler);
    App::getService('syslogger')->pushHandler($handler);
    App::getService('paylogger')->pushHandler($handler);
    App::getService('console_logger')->pushHandler($handler);
}
