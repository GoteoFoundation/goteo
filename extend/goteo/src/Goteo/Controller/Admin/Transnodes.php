<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Library\Mail,
		Goteo\Library\Template,
        Goteo\Model;

    class Transnodes {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors  = array();

            switch ($action) {
                case 'edit':
                case 'assign':
                case 'unassign':

                    // a ver si tenemos nodo
                    if (empty($id) && !empty($_POST['node'])) {
                        $id = $_POST['node'];
                    }

                    if (!empty($id)) {
                        $node = Model\Node::getMini($id);
                    } else {
                        Message::Error('No hay nodo sobre la que operar');
                        throw new Redirection('/admin/transnodes');
                    }

                    // asignar o desasignar
                    // la id de revision llega en $id
                    // la id del usuario llega por get
                    $user = $_GET['user'];
                    if (!empty($user)) {
                        $userData = Model\User::getMini($user);

                        $assignation = new Model\User\Translate(array(
                            'item' => $node->id,
                            'type' => 'node',
                            'user' => $user
                        ));

                        switch ($action) {
                            case 'assign': // se la ponemos
                                $assignation->save($errors);
                                $what = 'Asignado';
                                break;
                            case 'unassign': // se la quitamos
                                $assignation->remove($errors);
                                $what = 'Desasignado';
                                break;
                        }

                        if (empty($errors)) {
                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($userData->id, 'user');
                            $log->populate($what . ' traduccion de nodo (admin)', '/admin/transnodes',
                                \vsprintf('El admin %s ha %s a %s la traducción del nodo %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', $what),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('node', $node->name, $node->id)
                            )));
                            $log->doAdmin('admin');
                            unset($log);
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }

                        throw new Redirection('/admin/transnodes/edit/'.$node->id);
                    }
                    // fin asignar o desasignar

                    $node->translators = Model\User\Translate::translators($id, 'node');
                    $translators = Model\User::getAll(array('role'=>'translator'));


                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'transnodes',
                            'file'   => 'edit',
                            'action' => $action,
                            'availables' => $availables,
                            'translators' => $translators,
                            'node'=> $node
                        )
                    );

                    break;
            }

            $nodes = Model\Node::getTranslates($filters);
            $admins = Model\Node::getAdmins();
            $translators = Model\User::getAll(array('role'=>'translator'));

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'transnodes',
                    'file' => 'list',
                    'nodes' => $nodes,
                    'filters' => $filters,
                    'fields'  => array('admin', 'translator'),
                    'admins' => $admins,
                    'translators' => $translators
                )
            );

        }

    }

}
