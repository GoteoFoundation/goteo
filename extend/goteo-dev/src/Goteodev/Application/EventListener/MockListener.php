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

use Goteo\Application\App;
use Goteo\Application\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MockListener implements EventSubscriberInterface
{

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if(!$event->isMasterRequest()) {
            return;
        }
        if(!App::debug()) return;

        $mocks = Config::get('plugins.goteo-dev.mocks');

        if(!$mocks || !is_array($mocks)) return;

        if($mocks['ip']) {
            // Mock IP for geolocation tests:
            $request->server->set('REMOTE_ADDR', $mocks['ip']);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array(
            // Events with high priority
            KernelEvents::REQUEST => array('onKernelRequest', 100),
        );
    }

}
