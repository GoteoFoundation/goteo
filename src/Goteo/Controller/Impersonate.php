<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Library\Feed,
        Goteo\Model\User,
        Goteo\Application\Message,
		Goteo\Application\Session,
		Goteo\Model\Node;

	class Impersonate extends \Goteo\Core\Controller {

	    /**
	     * Suplantando al usuario
         * TODO: mejorar esto con excepciones y capacidad para volver al usuario original
	     * @param string $id   user->id
	     */
		public function index () {

            $admin = Session::getUser();

            if ($_SERVER['REQUEST_METHOD'] === 'POST'
                && !empty($_POST['id'])
                && !empty($_POST['impersonate'])) {

                $impersonator = Session::getUser()->id;
                $user = User::get($_POST['id']);
                Session::onSessionDestroyed(function () use ($impersonator, $user) {
                    Message::info("User [$impersonator] converted to [" . $user->id . "]");
                });
                Session::destroy();
                Session::setUser($user);
                session::store('impersonating', true);
                session::store('impersonator', $impersonator);

                unset($_SESSION['admin_menu']);
                // si es administrador de nodo cargamos tambien su nodo
                if (isset(Session::getUser()->roles['admin'])) {
                    // posible admin de nodo
                    if ($node = Node::getAdminNode(Session::getUserId())) {
                        $_SESSION['admin_node'] = $node;
                    } else {
                        unset($user->roles['admin']);
                    }
                } else {
                    unset($_SESSION['admin_node']);
                }

                // Evento Feed
                $log = new Feed();
                $log->setTarget(Session::getUserId(), 'user');
                $log->populate('Suplantación usuario (admin)', '/admin/users', \vsprintf('El admin %s ha %s al usuario %s', array(
                    Feed::item('user', $admin->name, $admin->id),
                    Feed::item('relevant', 'Suplantado'),
                    Feed::item('user', Session::getUser()->name, Session::getUserId())
                )));
                $log->doAdmin('user');
                unset($log);


                throw new Redirection('/dashboard');

            }
            else {
                Message::error('Ha ocurrido un error');
                throw new Redirection('/dashboard');
            }
		}

    }

}
