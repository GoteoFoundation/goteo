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

        // si el usuario ya está validado debemos mantenerlo en entorno seguro
        // usamos la funcionalidad de salto entre nodos para mantener la sesión
        if (Config::get('ssl')
            && Session::isLogged()
            && !$request->isSecure()
        ) {
            $event->setResponse(new RedirectResponse( str_replace('http://', 'https://', $request->getUri())));
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

