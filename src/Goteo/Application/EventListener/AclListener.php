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

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Core\ACL;
use Goteo\Library\Text;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

//TODO: use symfony components for security

class AclListener extends AbstractListener {
	public function onRequest(GetResponseEvent $event) {
		//not need to do anything on sub-requests
		if (!$event->isMasterRequest()) {
			return;
		}

		$request = $event->getRequest();
		$uri     = $request->getPathInfo();
		if (!ACL::check($uri) && substr($uri, 0, 11) !== '/user/login') {
			throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
		}
	}

	public static function getSubscribedEvents() {
		return array(
			KernelEvents::REQUEST => 'onRequest',
		);
	}
}
