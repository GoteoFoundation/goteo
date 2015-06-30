<?php

namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


use Goteo\Core\Model;

use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Currency;
use Goteo\Library\Text;
use Goteo\Application\View;
use Goteo\Foil\Extension;

//
class UrlListener implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        /*
         * Pagina de en mantenimiento
         */
        if (Config::get('maintenance') && $request->getPathInfo() !== '/about/maintenance'
             && !$request->request->has('Num_operacion')
            ) {
            $event->setResponse(new RedirectResponse('/about/maintenance'));
            return;
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
        );
    }
}

