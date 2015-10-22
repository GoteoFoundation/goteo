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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Model;
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Application\View;
use Goteo\Application\Session;
use Goteo\Application\Cookie;
use Goteo\Application;
use Goteo\Model\Mail;
use Goteo\Library\Feed;
use Goteo\Library\Text;
use Goteo\Library\OAuth\SocialAuth;
use Goteo\Library\Listing;

class UserController extends \Goteo\Core\Controller {

    /**
     * Atajo al perfil de usuario.
     * @param string $id   Nombre de usuario
     */
    public function indexAction($id = '', $show = '') {
        // die("['/user/profile/' . $id . '/' . $show]");
        return new RedirectResponse('/user/profile/' . $id . ($show ? '/' . $show : ''));
    }

    /**
     * Redirects a user where its required, using Request, Session vars
     * @param  Model\User $user     El objecto User a logoear
     * @param  boolean   $redirect Si ha que procesar las directivas de redireccion o no
     * @return Model\User           El usuario si no hay redirección
     */
    static public function userRedirect(Request $request) {

        if ($request->request->has('return') && $request->request->get('return')) {
            return new RedirectResponse($request->request->get('return'));
        }
        if (Session::get('jumpto')) {
            return new RedirectResponse(Session::getAndDel('jumpto'));
        }
        if (isset(Session::getUser()->roles['admin']) || isset(Session::getUser()->roles['superadmin'])) {
            return new RedirectResponse('/admin');
        }

        //default to dashboard
        return new RedirectResponse('/dashboard');

    }

    /**
     * Cerrar sesión.
     */
    public function logoutAction() {
        $url = '/?lang=' . Session::get('lang');
        // Shadowing?
        if($shadowed_by = Session::get('shadowed_by')) {
            $user = Session::getUser();
            if($old_user = Model\User::get($shadowed_by[0])) {
                if($shadowed_by[2]) $url = $shadowed_by[2];
                Session::onSessionDestroyed(function () use ($shadowed_by, $user) {
                    Application\Message::error('User <strong>' . $user->name . ' ('. $user->id. ')</strong> returned to <strong>' . $shadowed_by[1] . ' ('. $shadowed_by[0]. ')</strong>');
                });
            }
        }
        Session::destroy();
        if($old_user) {
            Session::setUser($old_user);
        }
        return new RedirectResponse($url);
    }

    /**
     * Registro de usuario.
     */
    public function registerAction(Request $request) {
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

            $user = new Model\User();
            $user->userid = $vars['userid'];
            $user->name = $vars['username'];
            $user->email = $vars['email'];
            $user->password = $vars['password'];
            $user->active = true;
            $user->node = Config::get('current_node');

            $user->save($errors);

            if (empty($errors)) {
                Application\Message::info(Text::get('user-register-success'));
                // no confirmation..., direct login
                Session::setUser(Model\User::get($user->id));
                //Redirect
                return self::userRedirect($request);

            }
            foreach ($errors as $field => $text) {
                Application\Message::error($text);
            }
            $vars['errors'] = $errors;
        }

        return new Response(View::render('user/login', $vars));
    }

    /**
     * Registro de usuario desde oauth
     */
    public function oauthRegisterAction(Request $request) {

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

            $user = new Model\User();
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

            //print_R($user);print_r($oauth);die;
            //no hará falta comprovar la contraseña ni el estado del usuario
            $skip_validations = array('password', 'active');

            //si el email proviene del proveedor de oauth, podemos confiar en el y lo confirmamos por defecto
            if ($provider_email == $user->email) {
                $user->confirmed = 1;
            }

            $query = Model\User::query('SELECT id,password FROM user WHERE email = ?', array($user->email));
            if ($u = $query->fetchObject()) {
                if ($u->password == sha1($request->request->get('password'))) {
                    //ok, login en goteo e importar datos
                    //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar
                    if ($user = $oauth->goteoLogin(true)) {
                        //login!
                        Session::setUser($user, true);
                        //redirect
                        return self::userRedirect($request);
                    }
                    else {
                        //si no: registrar errores
                        Application\Message::error($oauth->last_error);
                        return new RedirectResponse(SEC_URL . '/user/login');
                    }
                } else {
                    // si tiene contraseña permitir vincular la cuenta,
                    // si no mensaje de error
                    if($u->password) {
                        if($request->getMethod() === 'POST') {
                            Application\Message::error(Text::get('login-fail'));
                        }
                        return new Response(View::render('user/confirm_account',
                                            array(
                                            'oauth' => $oauth,
                                            'user' => Model\User::get($u->id)
                                        )
                        ));
                    }
                    else {
                        Application\Message::error(Text::get('oauth-goteo-user-password-error'));
                    }
                }
            } elseif ($user->save($errors, $skip_validations)) {
                //si el usuario se ha creado correctamente, login en goteo e importacion de datos
                //y fuerza que pueda logear en caso de que no tenga contraseña o email sin confirmar
                if ($user = $oauth->goteoLogin(true)) {
                    //login!
                    Session::setUser($user, true);
                    // redirect
                    return self::userRedirect($request);
                }
                else {
                    //si no: registrar errores
                    Application\Message::error($oauth->last_error);
                }
            } elseif ($errors) {
                foreach ($errors as $err => $val) {
                    if ($err != 'email' && $err != 'userid')
                        Application\Message::error($val);
                }
            }
        }

        return new Response(View::render('user/confirm',
                        array(
                            'errors' => $errors,
                            'userid' => $userid,
                            'email' => $email,
                            'provider_email' => $provider_email,
                            'oauth' => $oauth
                        )
        ));
    }

    /**
     * Registro de usuario a traves de Oauth (libreria HybridOauth, openid, facebook, twitter, etc).
     */
    public function oauthAction(Request $request) {

        $errors = array();
        if ($provider = $request->query->get('provider')) {
            $oauth = new SocialAuth($provider);
            if ($oauth->authenticate()) {
                if ($user = $oauth->goteoLogin()) {
                    //login!
                    Session::setUser($user, true);
                    //redirect
                    return self::userRedirect($request);
                }
                else {
                    //si falla: error o formulario de confirmación
                    if ($oauth->error_type == 'user-not-exists') {
                        return new Response(View::render('user/confirm',[ 'oauth' => $oauth ]));
                    }
                    // existe usuario, formulario de vinculacion
                    elseif ($oauth->error_type == 'user-password-exists') {
                        Application\Message::error($oauth->last_error);
                        return new Response(View::render('user/confirm_account',
                                        array(
                                            'oauth' => $oauth,
                                            'user' => Model\User::get($oauth->user_data['username'])
                                        )
                        ));
                    }
                    else {
                        Application\Message::error($oauth->last_error);
                        return new RedirectResponse(SEC_URL . '/user/login');
                    }
                }
            }
            else {
                //si falla: error, si no siempre se redirige al proveedor
                Application\Message::error($oauth->last_error);
                return new RedirectResponse(SEC_URL . '/user/login');
            }
        }

        return new Response(View::render('user/login', [ 'errors' => $errors ] ));
    }

    /**
     * Registro instantáneo de usuario mediante email
     * (Si existe devuelve id pero no loguea)
     */
    static public function instantReg($email, $name = '') {

        $errors = array();

        // Si el email es de un usuario existente, asignar el aporte a ese usuario (no lo logueamos)
        $query = Model\User::query("
                SELECT
                    id,
                    name,
                    email
                FROM user
                WHERE BINARY email = :email
                ",
            array(
                ':email'    => trim($email)
            )
        );
        if($row = $query->fetchObject()) {
            if (!empty($row->id)) {
                Application\Message::error(Text::get('error-user-email-exists'));
                return false;
            }
        }


        // si no existe, creamos un registro de usuario enviando el mail (y lo dejamos logueado)
        // ponemos un random en user y contraseña (el modelo mandará el mail)
        if (empty($name)) $name = substr($email, 0, strpos($email, '@'));
        $userid = substr($email, 0, strpos($email, '@')) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9);

        $user = new Model\User();
        $user->userid = $userid;
        $user->name = $name;
        $user->email = $email;
        $user->password = $name;
        $user->active = false;
        $user->node = Config::get('current_node');

        if ($user->save($errors)) {
            Session::setUser(Model\User::get($user->id), true);
            Application\Message::info(Text::get('user-register-success'));
            return $user->id;
        }

        if (!empty($errors)) {
            Application\Message::error(implode('<br />', $errors));
        }

        return false;
    }

    /**
     * Modificación perfil de usuario.
     * Metodo Obsoleto porque esto lo hacen en el dashboard
     */
    public function editAction() {
        return new RedirectResponse('/dashboard/profile');
    }

    /**
     * Perfil público de usuario.
     *
     * @param string $id    Nombre de usuario
     */
    public function profileAction($id = '', $show = 'profile', $category = '') {

        if (!in_array($show, array('profile', 'investors', 'sharemates', 'message'))) {
            $show = 'profile';
        }

        if($id) $user = Model\User::get($id, Lang::current());
        else    $user = Session::getUser();

        if (!$user instanceof Model\User || $user->hide) {
            throw new ControllerException(Text::get('fatal-error-user'));
        }

        //--- para usuarios públicos---
        if (!Session::isLogged()) {
            // la subpágina de mensaje también está restringida
            if ($show == 'message') {
                Session::store('jumpto', '/user/profile/' . $user->id . '/message');
                Application\Message::error(Text::get('user-login-required-to_message'));
                return new RedirectResponse(SEC_URL . '/user/login');
            }


            // a menos que este perfil sea de un vip, no pueden verlo
            if (!isset($user->roles['vip'])) {
                Session::store('jumpto', '/user/profile/' . $user->id . '/' . $show);
                Application\Message::error(Text::get('user-login-required-to_see'));
                return new RedirectResponse(SEC_URL . '/user/login');
            }
        }

        // en la página de mensaje o en el perfil para pintar o no el botón
        if ($show == 'message' || $show == 'profile') {

            // ver si el usuario logueado (A)
            $uLoged = Session::getUserId();

            // puede enviar mensaje (mensajear)
            $user->messageable = false;  // por defecto no

            // al usuario del perfil (B)
            $uProfile = $user->id;

            // solamente pueden comunicarse si:
            if (
                ( Model\User::isOwner($uLoged, true) && Model\User::isOwner($uProfile, true) )
                || Model\User::isInvestor($uLoged, $uProfile)
                || Model\User::isInvestor($uProfile, $uLoged)
                || Model\User::isParticipant($uProfile, $uLoged)
                || Model\User::isParticipant($uLoged, $uProfile)
            )
                $user->messageable = true;

        }

        // si ya esta en la página de mensaje
        if (0 && $show == 'message' && !$user->messageable) {
            Application\Message::error(Text::get('user-message-restricted'));
            return new RedirectResponse('/user/profile/' . $user->id);
        } else {
            // para el controller/message::personal
            Session::store('message_autorized', true);
        }


        $viewData = array();
        $viewData['user'] = $user;
        $viewData['worthcracy'] = \Goteo\Library\Worth::getAll();

        /* para sacar cofinanciadores */
        $projects = Model\Project::ofmine($user->id, true);
        $viewData['projects'] = $projects;

        //mis cofinanciadores
        $investors = Model\Invest::myInvestors($user->id, 5);
        $viewData['investors'] = $investors;

        // comparten intereses
        if ($show == 'profile'){
            $viewData['shares'] = Model\User\Interest::share($user->id, null, 6);
        }

        if ($show == 'sharemates') {

            $viewData['categories'] = Model\User\Interest::getAll($user->id);
            $shares = array();
            $limit = $category ? 20 : 6;
            foreach ($viewData['categories'] as $catId => $catName) {
                $gente = Model\User\Interest::share($user->id, $catId, $limit);
                if (count($gente) == 0) continue;
                $shares[$catId] = $gente;
            }
            $viewData['shares'] = $shares;

            if ($show == 'sharemates' && empty($viewData['shares'])) {
                $show = 'profile';
            }

        }

        if (!empty($category)) {
            $viewData['category'] = $category;
        }

        /* para sacar proyectos que cofinancio */
        // proyectos que cofinancio
        $invested = Model\User::invested($user->id, true);

        // agrupacion de proyectos que cofinancia y proyectos suyos
        $viewData['lists'] = array();
        if (!empty($invested)) {
            $viewData['lists']['invest_on'] = Listing::get($invested, 2);
        }
        if (!empty($projects)) {
            $viewData['lists']['my_projects'] = Listing::get($projects, 2);
        }

        return new Response(View::render('user/' . $show, $viewData));
    }

    /**
     * Activación usuario.
     *
     * @param type string	$token
     */
    public function activateAction($token) {
        $errors = array();
        $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
        if ($id = $query->fetchColumn()) {
            $user = Model\User::get($id);
            if (!$user->confirmed) {
                $user->confirmed = true;
                $user->active = true;
                if ($user->save($errors)) {
                    Application\Message::info(Text::get('user-activate-success'));
                    Session::setUser($user, true);

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($user->id, 'user');
                    $log->populate('nuevo usuario registrado (confirmado)', '/admin/users', Text::html('feed-new_user', Feed::item('user', $user->name, $user->id)));
                    $log->doAdmin('user');

                    // evento público
                    $log->title = $user->name;
                    $log->url = null;
                    $log->doPublic('community');

                    return new RedirectResponse('/dashboard');
                } else {
                    Application\Message::error($errors);
                }
            } else {
                Application\Message::error(Text::get('user-activate-already-active'));
            }
        } else {
            Application\Message::error(Text::get('user-activate-fail'));
        }

        return new RedirectResponse('/user/login');
    }

    /**
     * Cambiar dirección de correo.
     *
     * @param type string	$token
     */
    public function changeemailAction($token) {
        $token = \mybase64_decode($token);
        if (count(explode('¬', $token)) > 1) {
            $query = Model\User::query('SELECT id FROM user WHERE token = ?', array($token));
            if ($id = $query->fetchColumn()) {
                $user = Model\User::get($id);
                $user->email = $token;
                $errors = array();
                if ($user->save($errors)) {
                    Application\Message::info(Text::get('user-changeemail-success'));

                    // Refresca la sesión.
                    Model\User::flush();
                } else {
                    Application\Message::error($errors);
                }
            } else {
                Application\Message::error(Text::get('user-changeemail-fail'));
            }
        } else {
            Application\Message::error(Text::get('user-changeemail-fail'));
        }
        return new RedirectResponse('/dashboard');
    }

    /**
     * Recuperacion de contraseña
     * - Si no llega nada, mostrar formulario para que pongan su username y el email correspondiente
     * - Si llega post es una peticion, comprobar que el username y el email que han puesto son válidos
     *      si no lo son, dejarlos en el formulario y mensaje de error
     *      si son válidos, enviar email con la url y mensaje de ok
     *
     * - Si llega un hash, verificar y darle acceso hasta su dashboard /profile/access para que la cambien
     *
     * @param string $token     Codigo
     */
    public function recoverAction($token = '', Request $request) {

        $vars = array();

        // si el token mola, logueo este usuario y lo llevo a su dashboard
        // TODO:
        // CAMBIAR ESTE FUNCIONAMIENTO por uno mas simple basado en poner la nueva contraseña y verificar el link
        if ($token) {
            $token = \mybase64_decode($token);
            $parts = explode('¬', $token);
            if (count($parts) > 1) {
                $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                if ($id = $query->fetchColumn()) {
                    // el token coincide con el email y he obtenido una id
                    // Activamos y dejamos de esconder el usuario
                    Model\User::query('UPDATE user SET active = 1, hide = 0, confirmed = 1 WHERE id = ?', array($id));
                    $user = Model\User::get($id);
                    Session::setUser($user, true);
                    return $this->redirect('/dashboard/profile/access/recover#password');
                }
            }

            $vars['error'] = Text::get('recover-token-incorrect');
        }

        if ($request->getMethod() === 'POST' && $request->request->get('recover')) {
            $email = $request->request->get('email');
            if ($email && Model\User::recover($email)) {
                $vars['message'] = Text::get('recover-email-sended');
            } else {
                $vars['error'] = Text::get('recover-request-fail');
                $vars['email'] = $email;
            }
        }
        if($vars['error']) {
            Application\Message::error($vars['error']);
        }
        return $this->viewResponse('user/recover', $vars);
    }

    /**
     * Darse de baja
     * - Si no llega nada, mostrar formulario para que pongan el email de su cuenta
     * - Si llega post es una peticion, comprobar que el email que han puesto es válido
     *      si no es, dejarlos en el formulario y mensaje de error
     *      si es válido, enviar email con la url y mensaje de ok
     *
     * - Si llega un hash, verificar y dar de baja la cuenta (desactivar y ocultar)
     *
     * @param string $token     Codigo
     */
    public function leaveAction($token = '', Request $request) {

        $vars = array();

        // si el token mola, lo doy de baja
        if ($token) {
            $token = \mybase64_decode($token);
            $parts = explode('¬', $token);
            if (count($parts) > 1) {
                $query = Model\User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                if ($id = $query->fetchColumn()) {
                    if (!empty($id)) {
                        // el token coincide con el email y he obtenido una id
                        if (Model\User::cancel($id)) {
                            Application\Message::info(Text::get('leave-process-completed'));
                            return new RedirectResponse('/user/login');
                        } else {
                            Application\Message::error(Text::get('leave-process-fail', Config::get('mail.contact')));
                            return new RedirectResponse('/user/login');
                        }
                    }
                }
            }

            $vars['error'] = Text::get('leave-token-incorrect');
        }

        $email = $request->query->get('email');;
        if ($request->getMethod() === 'POST' && $request->request->get('leaving')) {
            $email = $request->request->get('email');
            $reason = $request->request->get('reason');
            if (Model\User::leaving($email, $reason)) {
                $vars['message'] = Text::get('leave-email-sended');
                Application\Message::error($vars['message']);
            } else {
                $vars['error'] = Text::get('leave-request-fail');
            }
        }
        $vars['email'] = $email;

        if($vars['error']) {
            Application\Message::error($vars['error']);
        }

        return new Response(View::render('user/leave', $vars));
    }

    /*
     * Método para bloquear el envío de newsletter
     *
     * token es un
     *
     */
    public function unsubscribeAction($token = '') {

        $errors = array();
        // si el token mola, lo doy de baja
        list($email, $mail_id) = Mail::decodeToken($token);
        if ($email) {
            $query = Model\User::query('SELECT id FROM user WHERE email = ?', array($email));
            if ($id = $query->fetchColumn()) {
                if (!empty($id)) {
                    // el token coincide con el email y he obtenido una id
                    Model\User::setPreferences($id, array('mailing'=>1), $errors);

                    if (empty($errors)) {
                        $message = Text::get('unsuscribe-request-success');
                    } else {
                        $error = implode('<br />', $errors);
                    }
                }
            } else {
                $error = Text::get('leave-token-incorrect');
            }
        } else {
            $error = Text::get('leave-request-fail');
        }

        if($message) {
            Application\Message::info($message);
        }
        if($error) {
            Application\Message::error($error);
        }
        return new Response(View::render('user/unsubscribe',
            array(
                'error' => $error,
                'message' => $message
            )
        ));
    }


}

