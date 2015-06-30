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

        // clean all caches if requested
        // TODO: replace by some controller
        if ($request->query->has('cleancache')) {
            Model::cleanCache();
        }

        /**
         * Session.
         */
        Session::onSessionExpires(function () {
            Message::info(Text::get('session-expired'));
        });
        Session::onSessionDestroyed(function () {
            Message::info('That\'s all folks!');
        });

        // Mantain user in secure enviroment if logged and ssl config on
        if(is_array(Config::get('proxies'))) {
            $request->setTrustedProxies(Config::get('proxies'));
        }
        if (Config::get('ssl') && Session::isLogged() && !$request->isSecure()) {
            $event->setResponse(new RedirectResponse('https://' . $request->getHttpHost() . $request->getRequestUri()));
            return;
        }

        // set currency
        Session::store('currency', Currency::set()); // depending on request
        // Set lang
        Lang::setDefault();
        Lang::setFromGlobals($request);

    }

    public function onResponse(FilterResponseEvent $event)
    {

        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        // Cookie
        // the stupid law cookie
        if (!Cookie::exists('goteo_cookies')) {
            Cookie::store('goteo_cookies', '1');
            Message::info(Text::get('message-cookies'));
        }

        $response = $event->getResponse();
        //Are we shadowing some user?
        if($shadowed_by = Session::get('shadowed_by')) {
            $body = '<div class="user-shadowing-bar">Back to <a href="/user/logout">' . $shadowed_by[1] . '</a></div>';
            $content = $response->getContent();
            $pos = strpos($content, '<div id="header">');
            if($pos !== false) {
                $content = substr($content, 0, $pos + 17) . $body . substr($content, $pos + 17);
                $response->setContent($content);
                $event->setResponse($response);
            }
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

