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
class SessionListener implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event) {

        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Init session
        Session::start('goteo-'.Config::get('env'), Config::get('session.time'));

        //clean all caches if requested
        if ($request->query->has('cleancache')) {
            Model::cleanCache();
        }

        /* Iniciación constantes *_URL */

        /**
         * Session.
         */
        Session::onSessionExpires(function () {
            Message::info(Text::get('session-expired'));
        });
        Session::onSessionDestroyed(function () {
            Message::info('That\'s all folks!');
        });

        // si el usuario ya está validado debemos mantenerlo en entorno seguro
        // usamos la funcionalidad de salto entre nodos para mantener la sesión
        if (Config::get('ssl') && Session::isLogged() && !HTTPS_ON) {
            $event->setResponse(new RedirectResponse(SEC_URL . $request->server->get('REQUEST_URI')));
            return;
        }

        /*
         * Pagina de en mantenimiento
         */
        if (Config::get('maintenance') && $request->getPathInfo() !== '/about/maintenance'
             && !$request->request->has('Num_operacion')
            ) {
            $event->setResponse(new RedirectResponse('/about/maintenance'));
            return;
        }


        // set currency
        Session::store('currency', Currency::set()); // depending on request

        // Set lang
        Lang::setDefault(GOTEO_DEFAULT_LANG);
        Lang::setFromGlobals($request);


    }

    public function onResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        // Cookie
        // the stupid law cookie
        if (!Cookie::exists('goteo_cookies')) {
            Cookie::store('goteo_cookies', '1');
            Message::info(Text::get('message-cookies'));
        }

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::RESPONSE => 'onResponse'
        );
    }
}

