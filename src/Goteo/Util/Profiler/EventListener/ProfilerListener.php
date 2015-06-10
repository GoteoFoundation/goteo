<?php

namespace Goteo\Util\Profiler\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event;
use Symfony\Component\HttpFoundation\Response;

use Goteo\Util\Profiler\DebugProfiler;

class ProfilerListener implements EventSubscriberInterface
{
    public function onRequest(Event\GetResponseEvent $event) {
        DebugProfiler::addEvent($event);
    }

    public function onController(Event\FilterControllerEvent $event) {
        DebugProfiler::addEvent($event);
    }

    public function onView(Event\GetResponseForControllerResultEvent $event) {
        DebugProfiler::addEvent($event);
    }

    public function onResponse(Event\FilterResponseEvent $event) {
        DebugProfiler::addEvent($event);
        $response = $event->getResponse();
        if(!$event->isMasterRequest() || false === stripos($response->headers->get('Content-Type'), 'text/html')) {
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

    public function onFinishRequest(Event\FinishRequestEvent $event) {
    }

    public function onTerminate(Event\PostResponseEvent $event) {

    }

    public function onException(Event\GetResponseForExceptionEvent $event) {

    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest',
            KernelEvents::CONTROLLER => 'onController',
            KernelEvents::VIEW => 'onView',
            KernelEvents::RESPONSE => 'onResponse',
            KernelEvents::FINISH_REQUEST => 'onFinishRequest',
            KernelEvents::TERMINATE => 'onTerminate',
            KernelEvents::EXCEPTION => 'onException'
        );
    }
}

