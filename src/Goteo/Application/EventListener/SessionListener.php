<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Goteo\Application\Config;
use Goteo\Application\Cookie;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Model;
use Goteo\Library\Currency;
use Goteo\Library\Text;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

//

class SessionListener extends AbstractListener {
    public function onRequest(GetResponseEvent $event) {

        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        // Add trusted proxies
        if (is_array(Config::get('proxies'))) {
            $request->setTrustedProxies(Config::get('proxies'));
        }
        //non cookies for notifyAction on investController
        if (strpos($request->getPathInfo(), '/invest/notify/') === 0) {
            return;
        }

        // clean all caches if requested
        // TODO: replace by some controller
        if ($request->query->has('cleancache')) {
            Model::cleanCache();
        }

        // Init session
        //
        // if url_lang is defined set a common cookie for all domains
        if (Config::get('url.url_lang')) {
            $host = $request->getHost();
            $parts = explode('.', $host);
            $sub_lang = array_shift($parts);
            if($sub_lang == 'www') $sub_lang = Config::get('lang');
            if (Lang::exists($sub_lang)) {
                // reduce host: ca.goteo.org => goteo.org
                $host = implode('.', $parts);
            }
            ini_set('session.cookie_domain', ".$host");
        }
        Session::start('goteo-' . Config::get('env'), Config::get('session.time'));

        /**
         * Session.
         */
        Session::onSessionExpires(function () {
            Message::info(Text::get('session-expired'));
        });
        Session::onSessionDestroyed(function () {
            //Message::info('That\'s all folks!');
        });


        // Set lang
        $lang = Lang::setFromGlobals($request);
        // Cookie
        // the stupid cookie EU law
        if (!Cookie::exists('goteo_cookies')) {
            // print_r($_COOKIE);die('cooki');
            Cookie::store('goteo_cookies', 'ok');
            // print_r($_COOKIE);die('cooki');
            Message::info(Text::get('message-cookies'));
        }


        $url = $request->getHttpHost();
        // Redirect to proper URL if url_lang is defined
        if (Config::get('url.url_lang')) {
            $parts = explode('.', $url);
            $sub_lang = $parts[0];
            if($sub_lang == 'www') $sub_lang = Config::get('lang');
            if (Lang::exists($sub_lang)) {
                // reduce url: ca.goteo.org => goteo.org
                array_shift($parts);
                $url = implode('.', $parts);
            }
            // if reduced URL is the main domain, redirect to sub-level lang
            if(count($parts) == 2) {
                if($request->query->has('lang')) {
                    $request->query->remove('lang');
                }
                // Login controller should mantaing always the same URL to help browser
                if(in_array($request->getPathInfo(), ['/login', '/password-recovery', '/password-reset', '/signup'])) {
                    // $url = "$url";
                    $request->query->set('lang', $lang);
                }
                // Main platform language will be shown as www subdomain
                elseif($lang && Config::get('lang') != $lang) {
                    $url = "$lang.$url";
                } else {
                    $url = "www.$url";
                }

            }
            // print_r($parts);echo "$url [$sub_lang=>$lang] ";die;
        }

        // Mantain user in secure enviroment if logged and ssl config on
        if (Config::get('ssl') && Session::isLogged() && !$request->isSecure()) {
            // Force HTTPS redirection
            $url = 'https://' . $url;
        } else {
            // Conserve the current scheme
            $url = $request->getScheme() . '://' . $url;
        }

        // Redirect if needed
        if ($url != $request->getScheme() . '://' . $request->getHttpHost()) {
            $query = http_build_query($request->query->all());
            // die($url . $request->getPathInfo() . ($query ? "?$query" : ''));
            // $event->setResponse(new RedirectdirectResponse($url . $request->getRequestUri()));
            $event->setResponse(new RedirectResponse($url . $request->getPathInfo() . ($query ? "?$query" : '')));
            return;
        }
        // die("[$url] - " .$request->getScheme() . '://' . $request->getHttpHost());

        // set currency
        $currency = $request->query->get('currency');
        if ($amount = $request->query->get('amount')) {
            $currency = (string) substr($amount, strlen((int) $amount));
        }
        if (empty($currency)) {
            $currency = Currency::current('id');
        }

        //ensure is a valid currency
        $currency = Currency::get($currency, 'id');
        Session::store('currency', $currency); // depending on request

        // extend the life of the session
        Session::renew();

    }

    /**
     * Modifies the html to add some data
     * @param  FilterResponseEvent $event [description]
     * @return [type]                     [description]
     */
    public function onResponse(FilterResponseEvent $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();
        //not need to do anything on sub-requests
        //Only in html content-type
        if (!$event->isMasterRequest() || false === stripos($response->headers->get('Content-Type'), 'text/html') || $request->isXmlHttpRequest()) {
            return;
        }

        $vars = [
            'code' => $response->getStatusCode(),
            // 'method' => $request->getMethod(),
            // 'ip' => $request->getClientIp(),
            'agent' => $request->headers->get('User-Agent'),
            'referer' => $request->headers->get('referer'),
            // 'http_user' => $request->getUser(),
            // 'uri' => $request->getUri(),
            'path' => $request->getPathInfo(),
            'query' => $request->query->all(),
            'user' => Session::getUserId(),
            'time' => microtime(true) - Session::getStartTime(),
            'route' => $request->attributes->get('_route'),
        ];

        $controller = $request->attributes->get('_controller');
        if (is_string($controller) && strpos($controller, '::') !== false) {
            $c = explode('::', $controller);
            $vars['controller'] = $c[0];
            $vars['action'] = $c[1];

        } else {
            $vars['controller'] = $controller;
        }
        // if ($route_params = $request->attributes->get('_route_params')) {
        //     $vars['route_params'] = $route_params;
        // }

        $this->info('Request', $vars);

        //Are we shadowing some user? let's add a nice bar to return to the original user
        if ($shadowed_by = Session::get('shadowed_by')) {
            // die(print_r(Session::get('shadowed_by')));
            $body = '<div class="user-shadowing-bar">Back to <a href="/user/logout">' . $shadowed_by[1] . '</a></div>';
            $content = $response->getContent();
            $search = '<div id="header">';

            if(View::getTheme() == 'responsive') {
                $search = '<body role="document">';
            }
            $pos = strpos($content, $search);
            if ($pos !== false) {
                $content = substr($content, 0, $pos + strlen($search)) . $body . substr($content, $pos + strlen($search));
                $response->setContent($content);
                $event->setResponse($response);
            }
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => array('onRequest', 50),
            KernelEvents::RESPONSE => array('onResponse', -50), // low priority: after headers are processed by symfony
        );
    }
}
