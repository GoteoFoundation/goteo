<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Core\ACL;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Library\Text;
use Goteo\Application\View;

//TODO: use symfony components for security
class AclListener implements EventSubscriberInterface
{
    public function onRequest(GetResponseEvent $event)
    {
        //not need to do anything on sub-requests
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $uri = $request->getPathInfo();
        if (!ACL::check($uri) && substr($uri, 0, 11) !== '/user/login') {
            //refresh permission status
            \Goteo\Model\User::flush();

            Message::error(Text::get('user-login-required-access'));
            if(Session::isLogged()) {
                $code = Response::HTTP_FORBIDDEN;
                // TODO: custom template
                $event->setResponse(new Response(View::render('errors/access_denied', ['msg' => 'Access denied', 'code' => $code], $code)));
                return;
            }
            $event->setResponse(new RedirectResponse('/user/login?return='.rawurlencode($uri)));
            return;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onRequest'
        );
    }
}

