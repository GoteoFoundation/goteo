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

use Goteo\Application\Message;
use Goteo\Application\App;
use Goteo\Library\Text;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterAuthEvent;

//TODO: use symfony components for security
class AuthListener implements EventSubscriberInterface
{
    public function loginSuccess(FilterAuthEvent $event)
    {
        $user = $event->getUser();
        // App::getService('logger')->debug('LOGIN SUCCEDEED: USERNAME: ' . $user->id . ' PASSWORD: ' . $user->password);
        App::getService('logger')->info('LOGIN SUCCEDEED: USERNAME: ' . $user->id);
    }

    public function loginFail(FilterAuthEvent $event)
    {
        $user = $event->getUser();
        // App::getService('logger')->debug('LOGIN FAILED: USERNAME: ' . $user->id . ' PASSWORD: ' . $user->password);
        App::getService('logger')->info('LOGIN FAILED: USERNAME: ' . $user->id);

        Message::error(Text::get('login-fail'));

    }

    public function loginRedundant(FilterAuthEvent $event) {
        $user = $event->getUser();
        App::getService('logger')->debug('LOGIN REPEATED: USERNAME: ' . $user->id);
    }

    public static function getSubscribedEvents()
    {
        return array(
            AppEvents::LOGIN_SUCCEEDED => 'loginSuccess',
            AppEvents::LOGIN_FAILED => 'loginFail',
            AppEvents::ALREADY_LOGGED => 'loginRedundant',
        );
    }
}

