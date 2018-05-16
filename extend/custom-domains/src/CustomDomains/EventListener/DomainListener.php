<?php

namespace CustomDomains\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\Config;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\View;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

//

class DomainListener extends AbstractListener {
    public static $main_domain;

    /**
     * Redirects to the proper custom domain if the path specified requires it
     */
    public function onRequest(GetResponseEvent $event) {
        $request = $event->getRequest();

        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        // Save a backup
        if(!self::$main_domain) self::$main_domain = preg_replace('!^https?://|^//!i','', Config::get('url.main'));
        $current_host = $request->getHttpHost();
        $current_path = $request->getPathInfo();
        $scheme = $request->getScheme();
        $domains = Config::get('plugins.custom-domains.domains');

        if($domains && is_array($domains)) {
            foreach($domains as $domain => $paths) {
                if(!is_array($paths)) $paths = [$paths];
                // If is a custom domain redirect if the path is not allowed
                if($domain === $current_host) {
                    foreach($paths as $path) {
                        if(strpos($current_path, $path) !== 0 && $current_path !== '/') {
                            // Redirect to normal url
                            // die("$scheme://" . self::$main_domain . $current_path);
                            $event->setResponse(new RedirectResponse("$scheme://" . self::$main_domain .  $current_path));
                        }
                        if($current_path === $path) {
                            // Redirect to custom domain on the index path
                            // die("$scheme://$domain");
                            $event->setResponse(new RedirectResponse("$scheme://$domain"));
                        }
                    }
                    // continue; // This domain is allowed, do not further redirect
                } else {
                    // Redirect to the proper domain if has the same prefix
                    // and not the same host
                    foreach($paths as $path) {
                        if(strpos($current_path, $path) === 0) {
                            // redirect to the alternate domain (do no add prefix if is the same)
                            $p = $current_path === $path ? '' : $current_path;
                            $event->setResponse(new RedirectResponse("$scheme://$domain$p"));
                            return;
                        }
                    }
                }
            }
        }
    }


    /**
     * Disables the path part for the domain by assigning the proper controller
     */
    public function onController(FilterControllerEvent $event) {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }


        $domains = Config::get('plugins.custom-domains.domains');
        if(!$domains || !is_array($domains)) return;

        $controller = $event->getController();
        $request = $event->getRequest();
        $current_host = $request->getHttpHost();
        $current_path = $request->getPathInfo();
        $scheme = $request->getScheme();

        // This only applies to the index route
        if($current_path === '/') {
            // Check if is a custom domain
            $redirect = '';
            foreach($domains as $domain => $paths) {
                if($domain === $current_host) {
                    if(!is_array($paths)) $paths = [$paths];
                    foreach($paths as $path) {
                        if($path !== '/') $redirect = "$scheme://$domain$path";

                        try {
                            // Find the right controller (if exists)
                            $matcher = App::getService('matcher');
                            $resolver = App::getService('resolver');
                            // $matcher->setContext(new RequestContext('/'));
                            $parameters = $matcher->match($path);
                            // print_r($parameters);die("$path $current_path");
                            if($parameters && $parameters['_controller']) {
                                // Change the request
                                $request->server->set('REQUEST_URI', $path);
                                $request->initialize($request->query->all(), $request->request->all(), $parameters, $request->cookies->all(), $request->files->all(), $request->server->all(), $request->getContent());

                                // get the controller parsed as symfony wants it
                                $controller = $resolver->getController($request);
                                // print_r($controller);
                                // Overwrite controller and exit
                                return $event->setController($controller);
                            }
                        } catch(ResourceNotFoundException $e) {

                        }
                    }
                }
            }
            // redirect to the first path if no controller found
            if($redirect) {
                $event->setController(function() use($redirect) {
                    return new RedirectResponse("$redirect");
                });
            }
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::REQUEST    => ['onRequest',  200], // Default priority, we want this to be executed
                                                     // after SessionListener (for language management)
            KernelEvents::CONTROLLER => 'onController'
        );
    }
}
