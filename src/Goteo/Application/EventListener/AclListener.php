<?php
namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Core\ACL;
use Goteo\Application\Message;
use Goteo\Library\Text;

//TODO: use symfony components for security
class AclListener implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->server->get('PATH_INFO');
        if (!ACL::check($uri) && substr($uri, 0, 11) !== '/user/login') {

            Message::Info(Text::get('user-login-required-access'));
            return new RedirectResponse(SEC_URL . '/user/login/?return='.rawurlencode($uri));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onRequest');
    }
}

