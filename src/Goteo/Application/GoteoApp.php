<?php

namespace Goteo\Application;

use Symfony\Component\Routing;
use Symfony\Component\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GoteoApp extends HttpKernel\HttpKernel
{
    public function __construct($routes)
    {
        $context = new Routing\RequestContext();
        $matcher = new Routing\Matcher\UrlMatcher($routes, $context);
        $resolver = new HttpKernel\Controller\ControllerResolver();

        $dispatcher = new EventDispatcher();
        //Security ACL
        $dispatcher->addSubscriber(new EventListener\AclListener($request));
        //Lang, cookies info, etc
        $dispatcher->addSubscriber(new EventListener\SessionListener($request));
        //Routes
        $dispatcher->addSubscriber(new HttpKernel\EventListener\RouterListener($matcher));
        //Control 404 and other errors
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ExceptionListener('Goteo\\Controller\\ErrorController::exceptionAction'));
        // Streamed responses
        // $dispatcher->addSubscriber(new HttpKernel\EventListener\StreamedResponseListener());
        //Automatic HTTP correct specifications
        $dispatcher->addSubscriber(new HttpKernel\EventListener\ResponseListener('UTF-8'));

        //TODO: debug toolbar for queries

        parent::__construct($dispatcher, $resolver);
    }
}
