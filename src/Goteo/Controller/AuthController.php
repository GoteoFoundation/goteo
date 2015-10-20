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
use Goteo\Application\Config;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterAuthEvent;
use Goteo\Application\Message;
use Goteo\Application\View;

use Goteo\Library\OAuth\SocialAuth;
use Goteo\Library\Text;

use Goteo\Model\User;


class AuthController extends \Goteo\Core\Controller {

    public function __construct() {
        // changing to a responsive theme here
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


        return $this->viewResponse('auth/login', ['return' => $request->query->get('return')]);

    }
    /**
     * Cerrar sesión.
     * TODO: change to a event dispatcher
     */
    public function logoutAction(Request $request) {
        $url = $request->headers->get('referer');
        if(empty($url) || strpos($url, '/logout') !== false) {
            $url = '/?lang=' . Session::get('lang');
        }
        // $url = $request->getUri();
        // Shadowing?
        $user = Session::getUser();
        if($shadowed_by = Session::get('shadowed_by')) {
            if($old_user = User::get($shadowed_by[0])) {
                if($shadowed_by[2]) $url = $shadowed_by[2];
                Session::onSessionDestroyed(function () use ($shadowed_by, $user) {
                    Message::error('User <strong>' . $user->name . ' ('. $user->id. ')</strong> returned to <strong>' . $shadowed_by[1] . ' ('. $shadowed_by[0]. ')</strong>');
                });
            }
        }
        Session::destroy();
        $this->dispatch(AppEvents::LOGOUT, new FilterAuthEvent($user));
        if($old_user) {
            Session::setUser($old_user);
        }
        return $this->redirect($url);
    }

    public function signupAction(Request $request)
    {
        // Already logged?
        if (Session::isLogged()) {
            return $this->dispatch(AppEvents::ALREADY_LOGGED, new FilterAuthEvent(Session::getUser()))->getUserRedirect($request);
        }

        $vars = [];


        if ($request->getMethod() == 'POST') {
            foreach ($request->request->all() as $key => $value) {
                $vars[$key] = trim($value);
            }

            $errors = array();

            if (strcmp($vars['email'], $vars['remail']) !== 0) {
                $errors['remail'] = Text::get('error-register-email-confirm');
            }
            if (strcmp($vars['password'], $vars['rpassword']) !== 0) {
                $errors['rpassword'] = Text::get('error-register-password-confirm');
            }

            $user = new User();
            $user->userid = $vars['userid'];
            $user->name = $vars['username'];
            $user->email = $vars['email'];
            $user->password = $vars['password'];
            $user->active = true;
            $user->node = Config::get('current_node');

            $user->save($errors);

            if (empty($errors)) {

                Message::info(Text::get('user-register-success'));
                // no confirmation..., direct login
                Session::setUser(User::get($user->id));
                //Redirect
                 //Everything ok, redirecting
                return $this->dispatch(AppEvents::LOGIN_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);

            }
            foreach ($errors as $field => $text) {
                Message::error($text);
            }
            $vars['errors'] = $errors;
        }

         $vars['return'] = $request->query->get('return');

        return $this->viewResponse('auth/signup', $vars);

    }

    public function passwordRecoveryAction($token = '', Request $request)
    {

        $vars = array();

        // If the token is ok, login and redirect change password view

        if ($token) {
            $token = \mybase64_decode($token);
            $parts = explode('¬', $token);
            if (count($parts) > 1) {
                $query = User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                if ($id = $query->fetchColumn()) {
                    // el token coincide con el email y he obtenido una id
                    // Activamos y dejamos de esconder el usuario
                    User::query('UPDATE user SET active = 1, hide = 0, confirmed = 1 WHERE id = ?', array($id));
                    $user = User::get($id);
                    Session::setUser($user, true);
                    return $this->redirect('/password-reset');
                }
            }

            else {

            //$vars['error'] = Text::get('recover-token-incorrect');
            Message::error(Text::get('recover-token-incorrect'));
                        return $this->redirect('/login');


            }
        }

        if ($request->getMethod() === 'POST') {
            $email = $request->request->get('email');
            if ($email && User::recover($email)) {
                //$vars['message'] = Text::get('recover-email-sended');
                return $this->viewResponse(
                    'auth/partials/recover_modal_success',
                    array(
                        'email' => $email
                    )
                );
            } else {
                $vars['error'] = Text::get('recover-request-fail');
                $vars['email'] = $email;
                //Application\Message::error($vars['error']);
                return $this->viewResponse(
                    'auth/partials/recover_modal_error',
                    array(
                        'error' => $vars['error']
                    )
                );
            }
        }
        
    }

    public function passwordResetAction(Request $request)
    {

        // Already logged?
        if (Session::isLogged()) {
            return $this->viewResponse(
            'auth/reset_password',
            array(
                )
            );
        }

        else
            return $this->redirect('/login');


        /*return $this->viewResponse(
            'auth/reset_password',
            array(
                )
        );*/

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
                        return $this->viewResponse('auth/confirm',[ 'oauth' => $oauth ]);
                    }
                    // existe usuario, formulario de vinculacion
                    elseif ($oauth->error_type == 'user-password-exists') {
                        Message::error($oauth->last_error);
                        return $this->viewResponse('auth/confirm_account',
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

        return $this->viewResponse('auth/login', ['return' => $request->query->get('return')]);
    }

    /**
     * Registro de usuario desde oauth
     */
    public function oauthSignupAction(Request $request) {

        $userid = $request->request->get('userid');
        $email = $request->request->get('email');
        $provider_email = $request->request->get('provider_email');

        //Check if the register is by oauth
        if ($provider = $request->request->get('provider')) {
            $tokens = $request->request->get('tokens');

            $oauth = new SocialAuth($provider);
            //importar els tokens obtinguts anteriorment via POST
            if ($tokens[$oauth->provider]['token'])
                $oauth->tokens[$oauth->provider]['token'] = $tokens[$oauth->provider]['token'];
            if ($tokens[$oauth->provider]['secret'])
                $oauth->tokens[$oauth->provider]['secret'] = $tokens[$oauth->provider]['secret'];

            // print_r($tokens);print_r($oauth->tokens[$oauth->provider]);die;

            $user = new User();
            $user->userid = $userid;
            $user->email = $email;
            $user->active = true;

            //resta de dades
            foreach ($oauth->user_data as $k => $v) {
                if ($request->request->get($k)) {
                    $oauth->user_data[$k] = $request->request->get($k);
                    if (in_array($k, $oauth->import_user_data))
                        $user->$k = $request->request->get($k);
                }
            }
            //si no existe nombre, nos lo inventamos a partir del userid
            if (trim($user->name) == '') {
                $user->name = ucfirst($user->userid);
            }

            //no hará falta comprovar la contraseña ni el estado del usuario
            $skip_validations = array('password', 'active');

            //si el email proviene del proveedor de oauth, podemos confiar en el y lo confirmamos por defecto
            if ($provider_email == $user->email) {
                $user->confirmed = 1;
            }

            $query = User::query('SELECT id,password FROM user WHERE email = ?', array($user->email));
            if ($u = $query->fetchObject()) {
                if ($u->password == sha1($request->request->get('password'))) {
                    //ok, login en goteo e importar datos
                    //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar
                    if ($user = $oauth->goteoLogin(true)) {
                        //login!
                        Session::setUser($user, true);

                        //Everything ok, redirecting
                        return $this->dispatch(AppEvents::LOGIN_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);
                    }
                    else {
                        //si no: registrar errores
                        Message::error($oauth->last_error);
                        return $this->redirect('/login');
                    }
                } else {
                    // si tiene contraseña permitir vincular la cuenta,
                    // si no mensaje de error
                    if($u->password) {
                        if($request->isMethod('post') && !$request->request->has('userid')) {
                            // A subscriber will register a message
                            $this->dispatch(AppEvents::LOGIN_FAILED, new FilterAuthEvent($u));
                        }
                        return $this->viewResponse('auth/confirm_account',
                                            array(
                                            'oauth' => $oauth,
                                            'user' => User::get($u->id)
                                        )
                        );
                    }
                    else {
                        // no se puede vincular la cuenta por falta de contraseña
                        Message::error(Text::get('oauth-goteo-user-password-error'));
                        $this->dispatch(AppEvents::SIGNUP_FAILED, new FilterAuthEvent($u));
                        return $this->redirect('/login');
                    }
                }
            } elseif ($user->save($errors, $skip_validations)) {
                //si el usuario se ha creado correctamente, login en goteo e importacion de datos
                //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar
                if ($user = $oauth->goteoLogin(true)) {
                    //login!
                    Session::setUser($user, true);

                    //Everything ok, redirecting
                    return $this->dispatch(AppEvents::SIGNUP_SUCCEEDED, new FilterAuthEvent($user))->getUserRedirect($request);
                }
                else {
                    //si no: registrar errores
                    Message::error($oauth->last_error);
                    $this->dispatch(AppEvents::SIGNUP_FAILED, new FilterAuthEvent($user));
                }
            } elseif ($errors) {
                foreach ($errors as $err => $val) {
                    if ($err != 'email' && $err != 'userid')
                        Message::error($val);
                }
            }
        }
        else {
            return $this->redirect('/login');
        }

        return $this->viewResponse('auth/confirm',
                        array(
                            'errors' => $errors,
                            'userid' => $userid,
                            'email' => $email,
                            'provider_email' => $provider_email,
                            'oauth' => $oauth
                        )
        );
    }
}
