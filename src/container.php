<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

// example.com/src/container.php

use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Goteo\Application\Config;

$sc = new DependencyInjection\ContainerBuilder();

// Context and matcher
$sc->register('context', 'Symfony\Component\Routing\RequestContext');
$sc->register('matcher', 'Symfony\Component\Routing\Matcher\UrlMatcher')
    ->setArguments(array('%routes%', new Reference('context')))
;


// resolver for the HttpKernel handle()
$sc->register('resolver', 'Symfony\Component\HttpKernel\Controller\ControllerResolver');

// Router for the dispatcher
$sc->register('listener.router', 'Symfony\Component\HttpKernel\EventListener\RouterListener')
    ->setArguments(array(new Reference('matcher')))
;

// always utf-8 output, just in case...
$sc->register('listener.response', 'Symfony\Component\HttpKernel\EventListener\ResponseListener')
    ->setArguments(array('UTF-8'))
;

// Let's handle exceptions as 404 or 500 nice error pages
$sc->register('listener.exception', 'Symfony\Component\HttpKernel\EventListener\ExceptionListener')
    ->setArguments(array('Goteo\\Controller\\ErrorController::exceptionAction'))
;

// APP LISTENERS
// Nice Maintenance message, Other (fatal) thrown exceptions configuration
$sc->register('app.listener.exception', 'Goteo\Application\EventListener\ExceptionListener');
// Lang, cookies info, etc
$sc->register('app.listener.session', 'Goteo\Application\EventListener\SessionListener');
// Auth listener
$sc->register('app.listener.auth', 'Goteo\Application\EventListener\AuthListener');
// Invest listener
$sc->register('app.listener.invest', 'Goteo\Application\EventListener\InvestListener');
// Legacy Security ACL
$sc->register('app.listener.acl', 'Goteo\Application\EventListener\AclListener');

// TODO: add custom event listeners: feed, mail, etc

// Event Dispatcher object
$sc->register('dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher')
    ->addMethodCall('addSubscriber', array(new Reference('app.listener.exception')))
    ->addMethodCall('addSubscriber', array(new Reference('app.listener.session')))
    ->addMethodCall('addSubscriber', array(new Reference('app.listener.auth')))
    ->addMethodCall('addSubscriber', array(new Reference('app.listener.invest')))
    ->addMethodCall('addSubscriber', array(new Reference('app.listener.acl')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.router')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.response')))
    ->addMethodCall('addSubscriber', array(new Reference('listener.exception')))
;

if(!function_exists("monolog_level")) {
    function monolog_level($log_level) {
        if($log_level === 'debug')       return Monolog\Logger::DEBUG;
        elseif($log_level === 'info')    return Monolog\Logger::INFO;
        elseif($log_level === 'warning') return Monolog\Logger::WARNING;
        else                             return Monolog\Logger::ERROR;
    }
}

// Logger
// Log info/warning/error into files
// TODO: from config
$env = Config::get('env');
$sc->register('logger.handler', 'Monolog\Handler\StreamHandler')
    ->setArguments(array(GOTEO_LOG_PATH . "app_$env.log", monolog_level(Config::get('log.app'))))
;
$logger = $sc->register('logger', 'Monolog\Logger')
    ->setArguments(array('main', array(new Reference('logger.handler'))))
;

$sc->register('paylogger.handler', 'Monolog\Handler\StreamHandler')
    ->setArguments(array(GOTEO_LOG_PATH . "payment_$env.log", monolog_level(Config::get('log.payment'))))
;
$sc->register('paylogger.processor.web', 'Monolog\Processor\WebProcessor');
$sc->register('paylogger.processor.memory', 'Monolog\Processor\MemoryUsageProcessor');
$paylogger = $sc->register('paylogger', 'Monolog\Logger')
    ->setArguments(array('payment', array(new Reference('paylogger.handler'))))
    ->addMethodCall('pushProcessor', array(new Reference('paylogger.processor.web')))
    ->addMethodCall('pushProcessor', array(new Reference('paylogger.processor.memory')))
;

// error mail send if defined
if(Config::get('log.mail')) {
    $sc->register('logger.mail_handler', 'Goteo\Util\Monolog\Handler\MailHandler')
        ->setArguments(array(Goteo\Model\Mail::createFromHtml(Config::getMail('fail'),
                                                 '',
                                                 "WebApp error in [" . Config::get('url.main') . "] ",
                                                 "<pre>SERVER: " . print_R($_SERVER, 1) . "</pre>\n"
                             ), monolog_level(Config::get('log.mail'))))
    ;
    $logger->addMethodCall('pushHandler', array(new Reference('logger.mail_handler')))->addTag('mail');
    $paylogger->addMethodCall('pushHandler', array(new Reference('logger.mail_handler')));
}



// Goteo main app
$sc->register('app', 'Goteo\Application\App')
    ->setArguments(array(new Reference('dispatcher'), new Reference('resolver')))
;


return $sc;
