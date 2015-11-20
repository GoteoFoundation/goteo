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
		// $this->info('LOGIN FAILED', [$user, 'password' => $user->password]);
		$this->notice('Login succedeed', [$user]);
	}

	public function logout(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->info('Logout', [$user]);
	}

	public function signupSuccess(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->notice('Signup succedeed', [$user]);
	}

	public function loginFail(FilterAuthEvent $event) {
		$user = $event->getUser();
		// $this->info('LOGIN FAILED', [$user, 'password' => $user->password]);
		$this->info('Login failed', [$user]);

		Message::error(Text::get('login-fail'));

	}

	public function signupFail(FilterAuthEvent $event) {
		$user = $event->getUser();
		// $this->info('LOGIN FAILED', [$user, 'password' => $user->password]);
		$this->info('Signup failed', [$user]);

		Message::error(Text::get('login-fail'));

	}

	public function loginRedundant(FilterAuthEvent $event) {
		$user = $event->getUser();
		$this->debug('Login repeated', [$user]);
	}

	public function resetPassword(FilterAuthEvent $event) {
		$user = $event->getUser();

		Message::info(Text::get('password-changed-ok'));

		$this->info('Reset password succedeed', [$user]);
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
