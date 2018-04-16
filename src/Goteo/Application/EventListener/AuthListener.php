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

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterAuthEvent;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Model\User;

class AuthListener extends AbstractListener {

    // Checks remember-me cookie
    public function onRequest(GetResponseEvent $event) {
        if(!Session::isLogged() && $rememberme = Cookie::get('rememberme')) {

            list($id, $token) = explode('.', $rememberme);
            $user = User::get($id);
            $signature = hash('sha256', $token . '-'. Config::get('secret') . '-' . $user->id . '-'. $user->getPassword());
            if($user->rememberme === $signature) {
                // valid, login & renew cookie
                Session::setUser($user, true);
                $event = new FilterAuthEvent($user);
                $event->setProvider('rememberme');
                $this->loginSuccess($event);

            } else {
                // Remove cookie
                Cookie::del('rememberme');
                // Message::error('Automatic login failed!');
                $this->warning('Rememberme login failed', [$user]);
            }
        }
    }

	public function loginSuccess(FilterAuthEvent $event) {
		$user = $event->getUser();
        $provider = $event->getProvider();

        // Check remember me, add cookie if required
        if($provider === 'rememberme') {
            // Cookie valid for a month
            $token = hash('sha256', Config::get('secret') . '-' . bin2hex(openssl_random_pseudo_bytes(100)));
            $signature = hash('sha256', $token . '-'. Config::get('secret') . '-' . $user->id . '-'. $user->getPassword());
            if(User::setProperty($user->id, $signature, 'rememberme')) {
                Cookie::del('rememberme');
                Cookie::store('rememberme', $user->id . '.' . $token, 3600 * 24 * 30);
            }
        }

		$this->notice('Login succedeed', [$user, 'provider' => $provider]);
	}

	public function logout(FilterAuthEvent $event) {
		$user = $event->getUser();

        Cookie::del('rememberme');
        User::setProperty($user->id, '', 'rememberme');

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
            KernelEvents::REQUEST       => 'onRequest',
		);
	}
}
