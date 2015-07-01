<?php

namespace Goteo\Util\Profiler\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpFoundation\Response;

use Goteo\Util\Profiler\DebugProfiler;

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
        if(!$event->isMasterRequest() || false === stripos($response->headers->get('Content-Type'), 'text/html') || $request->isXmlHttpRequest()) {
            return;
        }
        // die($response->headers->get('Content-Type'));
        $head = DebugProfiler::getHeadContent();
        $body = DebugProfiler::getBodyContent();
        $content = $response->getContent();
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
            KernelEvents::REQUEST => array('onKernelRequest', 1024),
            KernelEvents::CONTROLLER => array('onKernelController', -100),
            KernelEvents::RESPONSE => array('onKernelResponse', -1024),
            KernelEvents::EXCEPTION => 'onKernelException',
            KernelEvents::TERMINATE => array('onKernelTerminate', -1024),
        );
    }
}

