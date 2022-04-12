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

use Goteo\Application\Session;
use Goteodev\Profiler\DebugProfiler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpKernel\KernelEvents;

class ProfilerListener implements EventSubscriberInterface
{
    public function onKernelRequest(Event\RequestEvent $event) {
        DebugProfiler::addEvent($event);
    }

    public function onKernelController(Event\ControllerEvent $event) {
        DebugProfiler::addEvent($event);
    }

    public function onKernelResponse(Event\ResponseEvent $event) {
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

    public function onKernelTerminate(Event\TerminateEvent $event) {

    }

    public function onKernelException(Event\ExceptionEvent $event) {

    }

    public static function getSubscribedEvents(): array
    {
        return array(
            // kernel.request must be registered as early as possible to not break
            // when an exception is thrown in any other kernel.request listener
            KernelEvents::REQUEST => array('onKernelRequest', 512),
            // Others events with low priority
            KernelEvents::CONTROLLER => array('onKernelController', -100),
            KernelEvents::RESPONSE => array('onKernelResponse', -512),
        );
    }
}

