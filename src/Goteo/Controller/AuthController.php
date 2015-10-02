<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Application\Session;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterAuthEvent;
use Goteo\Application\Message;
use Goteo\Application\View;

use Goteo\Library\OAuth\SocialAuth;

use Goteo\Model\User;


class AuthController extends \Goteo\Core\Controller {

    public function __construct() {
        // changin to a responsive theme here
        View::setTheme('responsive');
    }

    public function loginAction(Request $request)
    {
        // Already logged?
        if (Session::isLogged()) {
            return $this->dispatch(AppEvents::ALREADY_LOGGED, new FilterAuthEvent(Session::getUser()))->getUserRedirect($request);
        }

        // check username/password
        if ($request->request->has('username')) {
            $username = strtolower($request->request->get('username'));
            $password = $request->request->get('password');
            if (false !== ($user = (User::login($username, $password)))) {
                if(Session::setUser($user, true)) {
                    //Everything ok, redirecting
                    return $this->dispatch(AppEvents::LOGIN_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);
                }
            }

            // A subscriber will register a message
            $this->dispatch(AppEvents::LOGIN_FAILED, new FilterAuthEvent(new User(['id' => $username, 'password' => $password])));
        }


        return $this->viewResponse('auth/login');

    }

    public function signupAction(Request $request)
    {
        // Already logged?
        if (Session::isLogged()) {
            return $this->dispatch(AppEvents::ALREADY_LOGGED, new FilterAuthEvent(Session::getUser()))->getUserRedirect($request);
        }

        return $this->viewResponse('auth/signup');

    }

    public function resetPasswordAction(Request $request)
    {

        return $this->viewResponse(
            'auth/reset_password',
            array(

                )
        );

    }

     /**
     * Social Action using library Oauth (HybridOauth, openid, facebook, twitter, etc).
     */
    public function oauthAction($provider, Request $request) {

        $errors = array();
        if ($provider) {
            if($request->query->has('return')) {
                Session::store('jumpto', $request->query->get('return'));
            }
            $oauth = new SocialAuth($provider);
            if ($oauth->authenticate()) {
                if ($user = $oauth->goteoLogin()) {
                    //login!
                    Session::setUser($user, true);

                     //Everything ok, redirecting
                    return $this->dispatch(AppEvents::LOGIN_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);
                }
                else {
                    //si falla: error o formulario de confirmación
                    if ($oauth->error_type == 'user-not-exists') {
                        return $this->viewResponse('user/confirm',[ 'oauth' => $oauth ]);
                    }
                    // existe usuario, formulario de vinculacion
                    elseif ($oauth->error_type == 'user-password-exists') {
                        Message::error($oauth->last_error);
                        return $this->viewResponse('user/confirm_account',
                                        array(
                                            'oauth' => $oauth,
                                            'user' => User::get($oauth->user_data['username'])
                                        )
                        );
                    }
                    else {
                        Message::error($oauth->last_error);
                        return $this->redirect('/login');
                    }
                }
            }
            else {
                //si falla: error, si no siempre se redirige al proveedor
                Message::error($oauth->last_error);
                return $this->redirect('/login');
            }
        }

        return $this->viewResponse('auth/login');
    }


}
