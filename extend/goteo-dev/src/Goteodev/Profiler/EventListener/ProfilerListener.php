<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteodev\Profiler\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Goteodev\Profiler\DebugProfiler;
use Goteo\Application\Session;

class ProfilerListener implements EventSubscriberInterface
{
    public function onKernelRequest(Event\GetResponseEvent $event) {
        DebugProfiler::addEvent($event);
    }

    public function onKernelController(Event\FilterControllerEvent $event) {
        DebugProfiler::addEvent($event);
    }

    // public function onKernelView(Event\GetResponseForControllerResultEvent $event) {
    //     DebugProfiler::addEvent($event);
    // }

    public function onKernelResponse(Event\FilterResponseEvent $event) {
        DebugProfiler::addEvent($event);
        $response = $event->getResponse();
        $request = $event->getRequest();

        if($response->isRedirection()) {
            $redirections = Session::get('redirections', []);
            $redirections[] = "Redirection: " .$request->getUri() .' => ' . $response->getTargetUrl();
            Session::store("redirections", $redirections);
            return;
        }

        if(!$event->isMasterRequest() ||
            false === stripos($response->headers->get('Content-Type'), 'text/html') ||
            $request->isXmlHttpRequest() ||
            $response instanceOf StreamedResponse ||
            $request->query->has('pronto')) {
            return;
        }

        $head = DebugProfiler::getHeadContent();
        $body = DebugProfiler::getBodyContent();
        $content = $response->getContent();
        if(strpos($content, '/jquery.') === false) {
            $head .= '<script src="'. SRC_URL .'/assets/js/jquery-1.12.4.min.js"></script>';
        }
        $pos = strpos($content, '</head>');
        if($pos !== false) {
            $content = substr($content, 0, $pos) . $head . substr($content, $pos);
        }
        else {
            $content = $content . $head;
        }
        $pos = strpos($content, '</body>');
        if($pos !== false) {
            $content = substr($content, 0, $pos) . $body . substr($content, $pos);
        }
        else {
            $content = $content . $body;
        }
        $response->setContent($content);
        $event->setResponse($response);
    }


    public function onKernelTerminate(Event\PostResponseEvent $event) {

    }

    public function onKernelException(Event\GetResponseForExceptionEvent $event) {

    }

    public static function getSubscribedEvents()
    {
        return array(
            // kernel.request must be registered as early as possible to not break
            // when an exception is thrown in any other kernel.request listener
            KernelEvents::REQUEST => array('onKernelRequest', 512),
            // Others events with low priority
            KernelEvents::CONTROLLER => array('onKernelController', -100),
            KernelEvents::RESPONSE => array('onKernelResponse', -512),
            // KernelEvents::EXCEPTION => array('onKernelException', 512),
            // KernelEvents::TERMINATE => array('onKernelTerminate', -512),
        );
    }
}

