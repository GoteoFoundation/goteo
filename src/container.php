<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

use Goteo\Application\Config;
use Goteo\Application\App;
use Goteo\Application\CustomRouter;
use Goteo\Application\Templating\TwigEngine;
use Monolog\Formatter\LogstashFormatter;
use Symfony\Component\DependencyInjection;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\Routing\Generator\UrlGenerator;

$sc = new DependencyInjection\ContainerBuilder();

$sc->register('context', Symfony\Component\Routing\RequestContext::class)
   ->addMethodCall('fromRequest', array(App::getRequest()));
$sc->register('matcher', Symfony\Component\Routing\Matcher\UrlMatcher::class)
   ->setArguments(array('%routes%', new Reference('context')));

$env = Config::get('env');

$sc->register('logger.processor.web', Goteo\Util\Monolog\Processor\WebProcessor::class)
   ->setArguments(array(App::getRequest()));
$sc->register('logger.processor.uid', Monolog\Processor\UidProcessor::class);
$sc->register('logger.processor.memory', Monolog\Processor\MemoryUsageProcessor::class);
$sc->register('logger.processor.introspection', Monolog\Processor\IntrospectionProcessor::class)
   ->setArguments(array(monolog_level('error')));

$sc->register('logger.formatter', LogstashFormatter::class)
   ->setArguments(array("app_$env", gethostname(), null, 'ctxt_', LogstashFormatter::V1));
$sc->register('logger.handler', Monolog\Handler\StreamHandler::class)
   ->setArguments(array(GOTEO_LOG_PATH."app_$env.log", monolog_level(Config::get('log.app'))))
   ->addMethodCall('setFormatter', array(new Reference('logger.formatter')))
;
$logger = $sc->register('logger', 'Goteo\Util\Monolog\Logger')
    ->setArguments(array('main', array(new Reference('logger.handler'))))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.web')))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.memory')));

$routeCollection = App::getRoutes();
$sc->set('url_generator', new UrlGenerator(
    $routeCollection,
    $sc->get("context"),
    $sc->get("logger"),
    Config::get('lang')
));
$sc->register('router', CustomRouter::class)
    ->setArguments([
        new Reference('url_generator'),
        $routeCollection,
    ]);
$sc->register("twig", TwigEngine::class)
    ->setArguments([new Reference('url_generator')]);

$sc->register('console_logger.formatter', LogstashFormatter::class)
   ->setArguments(array("console_$env", gethostname(), null, 'ctxt_', LogstashFormatter::V1));

$sc->register('console_logger.handler', Monolog\Handler\StreamHandler::class)
   ->setArguments(array(GOTEO_LOG_PATH."console_$env.log", monolog_level(Config::get('log.console'))))
   ->addMethodCall('setFormatter', array(new Reference('console_logger.formatter')))
;
$cliLogger = $sc->register('console_logger', 'Goteo\Util\Monolog\Logger')
    ->setArguments(array('console', array(new Reference('console_logger.handler'))))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.uid')))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.memory')))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.introspection')));

$syslogger = $sc->register('syslogger', 'Goteo\Util\Monolog\Logger')
    ->setArguments(array('syslog', array(new Reference('logger.handler'))))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.web')))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.memory')));

$payLogger = $sc->register('paylogger', 'Goteo\Util\Monolog\Logger')
    ->setArguments(array('payment', array(new Reference('logger.handler'))))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.web')))
    ->addMethodCall('pushProcessor', array(new Reference('logger.processor.memory')));

if (Config::get('log.mail')) {
    $sc->register('logger.mail_handler.formatter', Monolog\Formatter\HtmlFormatter::class);
    $mailer = Goteo\Model\Mail::createFromHtml(Config::getMail('fail'), '', "WebApp error in [" . Config::get('url.main') . "]");
    $mail = $sc->register('logger.mail_handler', Goteo\Util\Monolog\Handler\MailHandler::class)
        ->setArguments(array($mailer, '', Goteo\Util\Monolog\Logger::DEBUG, true))// delayed sending
        ->addMethodCall('setFormatter', array(new Reference('logger.mail_handler.formatter')));
    $sc->register('logger.buffer_handler', Monolog\Handler\FingersCrossedHandler::class)
        ->setArguments(array(new Reference('logger.mail_handler'), monolog_level(Config::get('log.mail'))));
    $payLogger->addMethodCall('pushHandler', array(new Reference('logger.buffer_handler')));
    $cliLogger->addMethodCall('pushHandler', array(new Reference('logger.buffer_handler')));
    $logger->addMethodCall('pushHandler', array(new Reference('logger.buffer_handler')));
}

$sc->register('resolver', Symfony\Component\HttpKernel\Controller\ControllerResolver::class)
    ->setArguments([$logger]);

$requestStack = new RequestStack();
$sc->register('listener.router', Symfony\Component\HttpKernel\EventListener\RouterListener::class)
   ->setArguments(array(new Reference('matcher'), $requestStack, null, new Reference('logger')))
;

$sc->register('listener.response', Symfony\Component\HttpKernel\EventListener\ResponseListener::class)
   ->setArguments(array('UTF-8'))
;

$sc->register('app.listener.exception', Goteo\Application\EventListener\ExceptionListener::class)
   ->setArguments(array(new Reference('logger')));
$sc->register('app.listener.session', Goteo\Application\EventListener\SessionListener::class)
   ->setArguments(array(new Reference('logger')));
$sc->register('app.listener.auth', Goteo\Application\EventListener\AuthListener::class)
   ->setArguments(array(new Reference('logger')));
$sc->register('app.listener.origin', Goteo\Application\EventListener\OriginListener::class)
   ->setArguments(array(new Reference('logger')));
$sc->register('app.listener.project', Goteo\Application\EventListener\ProjectListener::class)
    ->setArguments(array(new Reference('logger')));
$sc->register('app.listener.invest', Goteo\Application\EventListener\InvestListener::class)
  ->setArguments(array(new Reference('paylogger')));

$sc->register('app.listener.blog_post', Goteo\Application\EventListener\BlogPostListener::class)
  ->setArguments(array(new Reference('logger')));

$sc->register('app.listener.stories', Goteo\Application\EventListener\StoriesListener::class)
  ->setArguments(array(new Reference('logger')));

$sc->register('app.listener.project_post', Goteo\Application\EventListener\ProjectPostListener::class)
  ->setArguments(array(new Reference('logger')));

$sc->register('app.listener.channel', Goteo\Application\EventListener\ProjectChannelListener::class)
  ->setArguments(array(new Reference('logger')));

$sc->register('app.listener.poolinvest', Goteo\Application\EventListener\PoolInvestListener::class)
  ->setArguments(array(new Reference('paylogger')));

$sc->register('app.listener.donateinvest', Goteo\Application\EventListener\DonateInvestListener::class)
  ->setArguments(array(new Reference('paylogger')));

// Legacy Security ACL
$sc->register('app.listener.acl', Goteo\Application\EventListener\AclListener::class)
   ->setArguments(array(new Reference('logger')));
$sc->register('app.listener.messages', Goteo\Application\EventListener\MessageListener::class)
   ->setArguments(array(new Reference('logger')));

$sc->register('app.forms', Goteo\Util\Form\FormBuilder::class);
$sc->register('app.forms.finder', Goteo\Util\Form\FormFinder::class);

// Matcher processor Finder (handles custom matchfunding cases)
// This finder may add listeners to the dispatcher
$sc->register('app.matcher.finder', Goteo\Util\MatcherProcessor\MatcherFinder::class)
    ->setArguments(array($sc));

$sc->register('app.md.parser', 'Parsedown')
   ->addMethodCall('setBreaksEnabled', [true])
   ->addMethodCall('setUrlsLinked', [true])
;
$sc->register('app.currency.converter', Goteo\Library\Converter::class);

$sc->register('dispatcher', Symfony\Component\EventDispatcher\EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('app.listener.exception')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.session')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.auth')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.origin')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.project')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.invest')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.poolinvest')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.donateinvest')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.messages')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.project_post')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.blog_post')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.stories')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.channel')])
    ->addMethodCall('addSubscriber', [new Reference('app.listener.acl')])
    ->addMethodCall('addSubscriber', [new Reference('listener.router')])
    ->addMethodCall('addSubscriber', [new Reference('listener.response')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.milestone')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.favourite')])
;

$sc->register('app', Goteo\Application\App::class)
   ->setArguments([
       new Reference('dispatcher'),
       new Reference('resolver'),
       $requestStack,
       new ArgumentResolver()
   ]);

$sc->register('console.listener.milestone', Goteo\Console\EventListener\ConsoleMilestoneListener::class)
  ->setArguments(array(new Reference('console_logger')));
$sc->register('console.listener.favourite', Goteo\Console\EventListener\ConsoleFavouriteListener::class)
  ->setArguments(array(new Reference('console_logger')));
$sc->register('console.listener.exception', Goteo\Console\EventListener\ConsoleExceptionListener::class)
   ->setArguments(array(new Reference('console_logger')));
$sc->register('console.listener.project', Goteo\Console\EventListener\ConsoleProjectListener::class)
   ->setArguments(array(new Reference('console_logger')));
$sc->register('console.listener.watcher', Goteo\Console\EventListener\ConsoleWatcherListener::class)
   ->setArguments(array(new Reference('console_logger')));
$sc->register('console.listener.invest', Goteo\Console\EventListener\ConsoleInvestListener::class)
   ->setArguments(array(new Reference('console_logger')));
$sc->register('console.listener.mailing', Goteo\Console\EventListener\ConsoleMailingListener::class)
   ->setArguments(array(new Reference('console_logger')));

$sc->register('console_dispatcher', Symfony\Component\EventDispatcher\EventDispatcher::class)
    ->addMethodCall('addSubscriber', [new Reference('console.listener.exception')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.project')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.watcher')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.invest')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.milestone')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.favourite')])
    ->addMethodCall('addSubscriber', [new Reference('console.listener.mailing')])
;

$sc->register('console', Goteo\Console\Console::class)
    ->setArguments([new Reference('console_dispatcher')]);

return $sc;
