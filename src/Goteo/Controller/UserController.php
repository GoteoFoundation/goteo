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
use Symfony\Component\HttpFoundation\RedirectResponse;

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Config;
use Goteo\Application\View;
use Goteo\Application\Lang;
use Goteo\Application\Session;
use Goteo\Application\Message;
use Goteo\Model\User;
use Goteo\Model\Invest;
use Goteo\Model\Project;
use Goteo\Model\Mail;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Library\Listing;

class UserController extends \Goteo\Core\Controller {

    public function __construct() {
        // changing to a responsive theme here
        View::setTheme('responsive');
    }


    /**
     * Atajo al perfil de usuario.
     * @param string $id   Nombre de usuario
     */
    public function indexAction($id = '', $show = '') {
        // die("['/user/profile/' . $id . '/' . $show]");
        return $this->redirect('/user/profile/' . $id . ($show ? '/' . $show : ''));
    }

    /**
     * Modificación perfil de usuario.
     * Metodo Obsoleto porque esto lo hacen en el dashboard
     */
    public function editAction() {
        return $this->redirect('/dashboard/profile');
    }

    /**
     * Perfil público de usuario.
     *
     * @param string $id    Nombre de usuario
     */
    public function profileAction($id = '', $show = 'profile', $category = '') {

        // This should be changed to a responsive view anytime (soon!)
        View::setTheme('default');

        if (!in_array($show, array('profile', 'investors', 'sharemates', 'message'))) {
            $show = 'profile';
        }

        if($id) $user = User::get($id, Lang::current());
        else    $user = Session::getUser();

        if (!$user instanceof User || $user->hide) {
            throw new ModelNotFoundException(Text::get('fatal-error-user'));
        }

        //--- para usuarios públicos---
        if (!Session::isLogged()) {
            // la subpágina de mensaje también está restringida
            if ($show == 'message') {
                Session::store('jumpto', '/user/profile/' . $user->id . '/message');
                Message::error(Text::get('user-login-required-to_message'));
                return $this->redirect(SEC_URL . '/user/login');
            }


            // a menos que este perfil sea de un vip, no pueden verlo
            if (!isset($user->roles['vip'])) {
                Session::store('jumpto', '/user/profile/' . $user->id . '/' . $show);
                Message::error(Text::get('user-login-required-to_see'));
                return $this->redirect(SEC_URL . '/user/login');
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
                ( User::isOwner($uLoged, true) && User::isOwner($uProfile, true) )
                || User::isInvestor($uLoged, $uProfile)
                || User::isInvestor($uProfile, $uLoged)
                || User::isParticipant($uProfile, $uLoged)
                || User::isParticipant($uLoged, $uProfile)
            )
                $user->messageable = true;

        }

        // si ya esta en la página de mensaje
        if (0 && $show == 'message' && !$user->messageable) {
            Message::error(Text::get('user-message-restricted'));
            return $this->redirect('/user/profile/' . $user->id);
        } else {
            // para el controller/message::personal
            Session::store('message_autorized', true);
        }


        $viewData = array();
        $viewData['user'] = $user;
        $viewData['worthcracy'] = \Goteo\Library\Worth::getAll();

        /* para sacar cofinanciadores */
        $projects = Project::ofmine($user->id, true);
        $viewData['projects'] = $projects;

        //mis cofinanciadores
        $investors = Invest::myInvestors($user->id, 5);
        $viewData['investors'] = $investors;

        // comparten intereses
        if ($show == 'profile'){
            $viewData['shares'] = User\Interest::share($user->id, null, 6);
        }

        if ($show == 'sharemates') {

            $viewData['categories'] = User\Interest::getAll($user->id);
            $shares = array();
            $limit = $category ? 20 : 6;
            foreach ($viewData['categories'] as $catId => $catName) {
                $gente = User\Interest::share($user->id, $catId, $limit);
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
        $invested = User::invested($user->id, true);

        // agrupacion de proyectos que cofinancia y proyectos suyos
        $viewData['lists'] = array();
        if (!empty($invested)) {
            $viewData['lists']['invest_on'] = Listing::get($invested, 2);
        }
        if (!empty($projects)) {
            $viewData['lists']['my_projects'] = Listing::get($projects, 2);
        }

        return $this->viewResponse('user/' . $show, $viewData);
    }

    /**
     * Activación usuario.
     *
     * @param type string	$token
     */
    public function activateAction($token) {
        $errors = array();
        $query = User::query('SELECT id FROM user WHERE token = ?', array($token));
        if ($id = $query->fetchColumn()) {
            $user = User::get($id);
            if (!$user->confirmed) {
                $user->confirmed = true;
                $user->active = true;
                if ($user->save($errors)) {
                    Message::info(Text::get('user-activate-success'));
                    Session::setUser($user, true);

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($user->id, 'user')
                        ->populate('feed-new-user-confirmed',
                                   '/admin/users',
                                    new FeedBody(null, null, 'feed-new_user', [
                                            Feed::item('user', $user->name, $user->id)
                                        ])
                        )
                        ->doAdmin('user');

                    // evento público
                    $log->title = $user->name;
                    $log->url = null;
                    $log->doPublic('community');

                    return $this->redirect('/dashboard');
                } else {
                    Message::error($errors);
                }
            } else {
                Message::error(Text::get('user-activate-already-active'));
            }
        } else {
            Message::error(Text::get('user-activate-fail'));
        }

        return $this->redirect('/user/login');
    }

    /**
     * Cambiar dirección de correo.
     *
     * @param type string	$token
     */
    public function changeemailAction($token) {
        $token = \mybase64_decode($token);
        if (count(explode('¬', $token)) > 1) {
            $query = User::query('SELECT id FROM user WHERE token = ?', array($token));
            $errors = [];
            if ($id = $query->fetchColumn()) {
                $user = User::get($id);
                if($user->setEmail($user->getToken(true), $errors, true)) {
                    Message::info(Text::get('user-changeemail-success'));
                    // Refresca la sesión.
                    User::flush();
                } else {
                    Message::error($errors);
                }
            } else {
               Message::error(Text::get('user-changeemail-invalid-token'));
            }
        } else {
            Message::error(Text::get('user-changeemail-fail'));
        }
        return $this->redirect('/dashboard/settings/access');
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

        View::setTheme('default');

        $vars = array();

        // si el token mola, lo doy de baja
        if ($token) {
            $token = \mybase64_decode($token);
            $parts = explode('¬', $token);
            if (count($parts) > 1) {
                $query = User::query('SELECT id FROM user WHERE email = ? AND token = ?', array($parts[1], $token));
                if ($id = $query->fetchColumn()) {
                    if (!empty($id)) {
                        // el token coincide con el email y he obtenido una id
                        if (User::cancel($id)) {
                            Message::info(Text::get('leave-process-completed'));
                            return $this->redirect('/user/login');
                        } else {
                            Message::error(Text::get('leave-process-fail', Config::get('mail.contact')));
                            return $this->redirect('/user/login');
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
            if (User::leaving($email, $reason)) {
                $vars['message'] = Text::get('leave-email-sended');
                Message::error($vars['message']);
            } else {
                $vars['error'] = Text::get('leave-request-fail', $email);
            }
        }
        $vars['email'] = $email;

        if($vars['error']) {
            Message::error($vars['error']);
        }

        return $this->viewResponse('user/leave', $vars);
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
            $query = User::query('SELECT id FROM user WHERE email = ?', array($email));
            if ($id = $query->fetchColumn()) {
                if (!empty($id)) {
                    // el token coincide con el email y he obtenido una id
                    User::setPreferences($id, array('mailing' => 1), $errors);

                    if (empty($errors)) {
                        $message = Text::get('unsubscribe-request-success', $email);
                    } else {
                        $error = implode('<br />', $errors);
                    }
                }
            } else {
                $error = Text::get('leave-token-incorrect');
            }
        } else {
            list($email, $mail_id) = Mail::decodeToken($token, false);
            $error = Text::get('leave-request-fail', $email);
        }

        if($message) {
            Message::info($message);
        }
        if($error) {
            Message::error($error);
        }
        return $this->viewResponse('user/unsubscribe',
            array(
                'error' => $error,
                'token' => Mail::encodeToken([$email]),
                'message' => $message
            )
        );
    }


    /*
     * Subscribe to newsletter by token
     *
     */
    public function subscribeAction($token = '') {
                $errors = array();
        // si el token mola, lo doy de baja
        list($email, $mail_id) = Mail::decodeToken($token);

        if ($email) {
            $query = User::query('SELECT id FROM user WHERE email = ?', array($email));
            if ($id = $query->fetchColumn()) {
                if (!empty($id)) {
                    // el token coincide con el email y he obtenido una id
                    User::setPreferences($id, array('mailing' => 0), $errors);

                    if (empty($errors)) {
                        $message = Text::get('subscribe-request-success', $email);
                    } else {
                        $error = implode('<br />', $errors);
                    }
                }
            } else {
                $error = Text::get('leave-token-incorrect');
            }
        } else {
            list($email, $mail_id) = Mail::decodeToken($token, false);
            $error = Text::get('leave-request-fail', $email);
        }

        if($message) {
            Message::info($message);
        }
        if($error) {
            Message::error($error);
        }
        return $this->viewResponse('user/subscribe',
            array(
                'error' => $error,
                'token' => Mail::encodeToken([$email]),
                'message' => $message
            )
        );
    }
}

