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

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterAuthEvent;
use Goteo\Application\Message;
use Goteo\Library\Text;

class AuthListener extends AbstractListener {
	public function loginSuccess(FilterAuthEvent $event) {
		$user = $event->getUser();
        $provider = $event->getProvider();

        // Check remember me, add cookie if required
        if($provider === 'rememberme') {
            die('cookie');
        }

		$this->notice('Login succedeed', [$user, 'provider' => $provider]);
	}

	public function logout(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->info('Logout', [$user, 'provider' => $event->getProvider()]);
	}

	public function signupSuccess(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->notice('Signup succedeed', [$user, 'provider' => $event->getProvider()]);
	}

	public function loginFail(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->info('Login failed', [$user, 'provider' => $event->getProvider()]);

		Message::error(Text::get('login-fail'));

	}

	public function signupFail(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->info('Signup failed', [$user, 'provider' => $event->getProvider()]);

		Message::error(Text::get('login-fail'));

	}

	public function loginRedundant(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->debug('Login repeated', [$user, 'provider' => $event->getProvider()]);
	}

	public function resetPassword(FilterAuthEvent $event) {
		$user = $event->getUser();

		Message::info(Text::get('password-changed-ok'));

		$this->info('Reset password succedeed', [$user, 'provider' => $event->getProvider()]);
	}

	public static function getSubscribedEvents() {
		return array(
			AppEvents::LOGIN_SUCCEEDED  => 'loginSuccess',
			AppEvents::SIGNUP_SUCCEEDED => 'signupSuccess',
			AppEvents::LOGIN_FAILED     => 'loginFail',
			AppEvents::LOGOUT           => 'logout',
			AppEvents::SIGNUP_FAILED    => 'signupFail',
			AppEvents::ALREADY_LOGGED   => 'loginRedundant',
			AppEvents::RESET_PASSWORD   => 'resetPassword',
		);
	}
}
