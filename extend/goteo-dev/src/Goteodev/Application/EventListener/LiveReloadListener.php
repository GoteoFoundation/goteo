<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteodev\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Goteo\Application\Config;
use Goteo\Application\App;

class LiveReloadListener implements EventSubscriberInterface
{

    public function onKernelResponse(ResponseEvent $event) {

        $response = $event->getResponse();
        $request = $event->getRequest();
        if(!$event->isMasterRequest() ||
            false === stripos($response->headers->get('Content-Type'), 'text/html') ||
            $request->isXmlHttpRequest() ||
            $response->isRedirection()) {
            return;
        }
        if(!App::debug()) return;

        $port = Config::get('plugins.goteo-dev.liveport');

        if(empty($port)) return;

        $html  = "\n\t<!-- Debug Javascript for developers -->\n\t";
        $html .= '<script src="//' . $request->getHost() . ':' . $port . '/livereload.js"></script>';
        $html .=  "\n\n";

        $content = $response->getContent();
        $pos = strpos($content, '</head>');
        if($pos !== false) {
            $response->setContent(substr($content, 0, $pos) . $html . substr($content, $pos));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            // Events with low priority
            KernelEvents::RESPONSE => array('onKernelResponse', -10),
        );
    }

}
