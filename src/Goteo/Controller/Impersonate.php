<?php

namespace Goteo\Controller {

	use Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Model\User,
		Goteo\Application\Session,
		Goteo\Model\Node;

	class Impersonate extends \Goteo\Core\Controller {

	    /**
	     * Suplantando al usuario
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
                    Message::Info("User [$impersonator] converted to [" . $user->id . "]");
                });
                Session::destroy();
                Session::setUser($user);
                session::store('impersonating', true);
                session::store('impersonator', $impersonator);

                unset($_SESSION['admin_menu']);
                // si es administrador de nodo cargamos tambien su nodo
                if (isset($_SESSION['user']->roles['admin'])) {
                    // posible admin de nodo
                    if ($node = Node::getAdminNode($_SESSION['user']->id)) {
                        $_SESSION['admin_node'] = $node;
                    } else {
                        unset($user->roles['admin']);
                    }
                } else {
                    unset($_SESSION['admin_node']);
                }

                // Evento Feed
                $log = new Feed();
                $log->setTarget($_SESSION['user']->id, 'user');
                $log->populate('Suplantación usuario (admin)', '/admin/users', \vsprintf('El admin %s ha %s al usuario %s', array(
                    Feed::item('user', $admin->name, $admin->id),
                    Feed::item('relevant', 'Suplantado'),
                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id)
                )));
                $log->doAdmin('user');
                unset($log);


                throw new Redirection('/dashboard');

            }
            else {
                Message::Error('Ha ocurrido un error');
                throw new Redirection('/dashboard');
            }
		}

    }

}
