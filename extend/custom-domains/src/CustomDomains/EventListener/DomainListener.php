<?php

namespace CustomDomains\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\Config;
use Goteo\Application\App;
use Goteo\Application\Lang;
use Goteo\Application\Session;
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
    protected $main_domain;
    protected $lang_domain = '';
    protected $url_lang = '';

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
        if(!$this->main_domain) $this->main_domain = preg_replace('!^https?://|^//!i','', Config::get('url.main'));
        $current_host = $request->getHttpHost();
        $current_path = $request->getPathInfo();
        $scheme = $request->getScheme();

        $redirects = Config::get('plugins.custom-domains.redirects');
        if($redirects && is_array($redirects)) {
            foreach($redirects as $domain => $destination) {
                // TODO: make it REGEXP
                if(strpos($current_host.$current_path, $domain) === 0) {
                    // die("Redirect from $domain to $destination");
                    $event->setResponse(new RedirectResponse($destination));
                    return;
                }
            }
        }

        $domains = Config::get('plugins.custom-domains.domains');

        if($domains && is_array($domains)) {
            foreach($domains as $domain => $paths) {
                if(!is_array($paths)) $paths = [$paths];
                // If is a custom domain redirect if the path is not allowed
                if($domain === $current_host) {
                    // Prevent SessionListener to redirect due lang
                    $this->url_lang = Config::get('url.url_lang');
                    Config::set('url.url_lang', '');
                    // Change the langs menu to show the proper host
                    $this->lang_domain = "$scheme://$domain";
                    // Lang::setLangUrl($domain);
                    $redirect = true;
                    foreach($paths as $path) {
                        if(strpos($current_path, $path) === 0 || $current_path === '/') {
                            $redirect = false;
                        }
                    }
                    // Redirect to normal url if not in the proper domain
                    if($redirect) {
                        // echo "$current_path|$path ";die("$scheme://" . $this->main_domain . $current_path);
                        $event->setResponse(new RedirectResponse("$scheme://" . $this->main_domain .  $current_path));
                        return;
                    }
                    // Redirect to custom domain on the index page if is the first path
                    if($current_path === $paths[0] && !$request->getQueryString()) {
                        // print_r($request->getQueryString());die("$scheme://$domain");
                        $event->setResponse(new RedirectResponse("$scheme://$domain"));
                        return;
                    }
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

        // Rebuild lang menu here, after SessionListener processing
        if($this->lang_domain) {
            Session::delMainMenuPosition(10); // remove lang
            // Langs
            $back = Lang::getLangUrl();
            Lang::setLangUrl($this->lang_domain);
            $langs = [];
            foreach (Lang::listAll('name', true) as $id => $lang) {
                if (Lang::isActive($id)) continue;
                $langs[Lang::getUrl($id, $request)] = $lang;
            }
            Session::addToMainMenu('<i class="fa fa-globe"></i> ' . Lang::getName(), $langs, 'langs', 10, 'main');
            // restore original behaviour for links in views
            Lang::setLangUrl($back);
            Config::get('url.url_lang', $this->url_lang);
        }

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
            KernelEvents::REQUEST    => ['onRequest',  100], // We want this to be executed
                                                             // before SessionListener (that handles language
                                                             // redirections if url.url_lang is active)
            KernelEvents::CONTROLLER => ['onController', 50]
        );
    }
}
