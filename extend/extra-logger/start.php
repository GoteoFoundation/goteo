<?php

use Goteo\Application\App;
use Goteo\Application\Config;
use Monolog\Handler\GelfHandler;
use Gelf\Publisher;
use Gelf\Transport\UdpTransport;

$gelf = Config::get('plugins.extra-logger.gelf');
if($gelf && $gelf["host"]) {
    $handler = new GelfHandler(new Publisher( new UdpTransport($gelf["host"], $gelf["port"]) ));
    App::getService('logger')->pushHandler($handler);
    App::getService('syslogger')->pushHandler($handler);
    App::getService('paylogger')->pushHandler($handler);
}
