<?php

namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application\Lang;
use Goteo\Library\Currency;
use Goteo\Library\Text;
use Goteo\Application\View;

//
class SessionListener implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event) {
        $request = $event->getRequest();
        /**
         * Session.
         */
        Session::start('goteo-'.GOTEO_ENV, defined('GOTEO_SESSION_TIME') ? GOTEO_SESSION_TIME : 3600);
        Session::onSessionExpires(function () {
            Message::info(Text::get('session-expired'));
        });
        Session::onSessionDestroyed(function () {
            Message::info('That\'s all folks!');
        });

        // if ssl enabled
        $SSL = (defined('GOTEO_SSL') && GOTEO_SSL === true );

        // segun sea nodo o central
        $SITE_URL = (NODE_ID != GOTEO_NODE) ? NODE_URL : GOTEO_URL;
        $raw_url = str_replace('http:', '', $SITE_URL);

        // SEC_URL (siempre https, si ssl activado)
        define('SEC_URL', ($SSL) ? 'https:'.$raw_url : $SITE_URL);

        // si estamos en entorno seguro
        define('HTTPS_ON', ($request->server->get('HTTPS') === 'on' || $request->server->get('HTTP_X_FORWARDED_PROTO') === 'https' ));

        // SITE_URL, según si estamos en entorno seguro o si el usuario esta autenticado
        if ($SSL && (\HTTPS_ON || Session::isLogged())) {
            define('SITE_URL', SEC_URL);
        } else {
            define('SITE_URL', $SITE_URL);
        }

        // si el usuario ya está validado debemos mantenerlo en entorno seguro
        // usamos la funcionalidad de salto entre nodos para mantener la sesión
        if ($SSL && Session::isLogged() && !\HTTPS_ON) {
            header('Location: ' . SEC_URL . $request->server->get('REQUEST_URI'));
            die;
        }
        /* Fin inicializacion constantes *_URL */


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

