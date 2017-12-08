<?php
/*
 * This file is part of the PrivateZones Plugin.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 */

namespace Goteo\Application\EventListener;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Library\Text;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BasicAuthListener extends AbstractListener {
    public function onRequest(GetResponseEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $uri     = $request->getPathInfo();

        $public = Config::get('plugins.private-zones.public');
        if(is_array($public)) {
            foreach($public as $path) {
                if(strpos($uri, $path) === 0) {
                    // allowed, public route
                    return;
                }
            }
        }

        $private = Config::get('plugins.private-zones.private');
        if($private && is_array($private)) {
            $users = [];
            foreach($private as $user => $ops) {
                if($ops['password'] && $ops['paths'] && is_array($ops['paths'])) {
                    foreach($ops['paths'] as $path) {
                        if(strpos($uri, $path) === 0) {
                            $users[$user] = $ops['password'];
                        }
                    }
                }
            }
            if($users) {
                // Do Http auth
                // echo "Auth $uri with " .print_r($users,1)."\n";
                // Status flag:
                $login_successful = false;
                // Check username and password:
                if ($request->server->has('PHP_AUTH_USER') && $request->server->has('PHP_AUTH_PW')){

                    $user = $request->server->get('PHP_AUTH_USER');
                    $pass = $request->server->get('PHP_AUTH_PW');
                    // print_r($request->server->all());die("$user $pass");

                    foreach($users as $u => $p) {
                        if($user === $u && $pass === $p) {
                            $login_successful = true;
                            $auth = Session::get('PrivateZonesAuth', [$user]);
                            Session::store('PrivateZonesAuth', $auth);
                            break;
                        }
                    }
                }
                if(!$login_successful) {
                    /*
                    ** The user gets here if:
                    **
                    ** 1. The user entered incorrect login data (three times)
                    **     --> User will see the error message from below
                    **
                    ** 2. Or the user requested the page for the first time
                    **     --> Then the 401 headers apply and the "login box" will
                    **         be shown
                    */

                    // The text inside the realm section will be visible for the
                    // user in the login box
                    $response = new Response();

                    // Customize your response object to display the exception details
                    $code = Response::HTTP_UNAUTHORIZED;
                    $response->setStatusCode($code);
                    $response->headers->set('WWW-Authenticate', sprintf('Basic realm="%s"', 'Private zone'));

                    View::setTheme('default');
                    $view = View::render('errors/access_denied', ['title' => 'HTTP AUTH FAILED', 'msg' => 'You need to be authenticated to access this page', 'code' => $code], $code);

                    $response->setContent($view);
                    $event->setResponse($response);
                    // print "Login failed!\n";

                }
                // die("[$login_successful]");
            }
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST => 'onRequest',
        );
    }
}
