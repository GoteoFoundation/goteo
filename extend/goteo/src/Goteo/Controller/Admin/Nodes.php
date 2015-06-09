<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Feed,
        Goteo\Application\Message,
        Goteo\Model;

        /**
         * Gestion canales por administradores
         */
    class Nodes {

        public static function process($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                switch ($_POST['action']) {
                    case 'add':

                        $url = "http://{$_POST['id']}.goteo.org";
                        // objeto
                        $node = new Model\Node(array(
                                    'id' => $_POST['id'],
                                    'name' => $_POST['name'],
                                    'email' => $_POST['email'],
                                    'url' => $url,
                                    'active' => $_POST['active']
                                ));

                        if ($node->create($errors)) {

                                Message::info('Canal creado');
                                $txt_log = 'creado';

                            // Evento feed
                            $log = new Feed();
                            $log->setTarget($node->id, 'node');
                            $log->populate('Canal gestionado desde admin', 'admin/nodes', \vsprintf('El admin %s ha %s el Canal %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('relevant', $txt_log),
                                        Feed::item('project', $node->name))
                                    ));
                            $log->doAdmin('admin');
                            unset($log);

                            if ($_POST['action'] == 'add') {
                                Message::info('Puedes asignar ahora sus administradores');
                                throw new Redirection('/admin/nodes/admins/' . $node->id);
                            }
                        } else {
                            Message::error('Fallo al crear, revisar los campos');

                            return new View(
                                            'admin/index.html.php',
                                            array(
                                                'folder' => 'nodes',
                                                'file' => 'add',
                                                'action' => 'add'
                                            )
                            );
                        }
                        break;
                    case 'edit':
                        // objeto
                        $node = new Model\Node(array(
                                    'id' => $_POST['id'],
                                    'name' => $_POST['name'],
                                    'email' => $_POST['email'],
                                    'url' => $_POST['url'],
                                    'active' => $_POST['active']
                                ));

                        if ($node->save($errors)) {

                                Message::info('Canal actualizado');
                                $txt_log = 'actualizado';

                            // Evento feed
                            $log = new Feed();
                            $log->setTarget($node->id, 'node');
                            $log->populate('Canal gestionado desde admin', 'admin/nodes', \vsprintf('El admin %s ha %s el Canal %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('relevant', $txt_log),
                                        Feed::item('project', $node->name))
                                    ));
                            $log->doAdmin('admin');
                            unset($log);

                            if ($_POST['action'] == 'add') {
                                Message::info('Puedes asignar ahora sus administradores');
                                throw new Redirection('/admin/nodes/admins/' . $node->id);
                            }
                        } else {
                            Message::error('Fallo al actualizar, revisar los campos');

                            return new View(
                                            'admin/index.html.php',
                                            array(
                                                'folder' => 'nodes',
                                                'file' => 'edit',
                                                'action' => 'edit',
                                                'node' => $node
                                            )
                            );
                        }
                        break;
                }
            }

            switch ($action) {
                case 'add':
                    return new View(
                                    'admin/index.html.php',
                                    array(
                                        'folder' => 'nodes',
                                        'file' => 'add',
                                        'action' => 'add',
                                        'node' => null
                                    )
                    );
                    break;
                case 'edit':
                    $node = Model\Node::get($id);

                    return new View(
                                    'admin/index.html.php',
                                    array(
                                        'folder' => 'nodes',
                                        'file' => 'edit',
                                        'action' => 'edit',
                                        'node' => $node
                                    )
                    );
                    break;
                case 'admins':
                    $node = Model\Node::get($id);

                    if (isset($_GET['op']) && isset($_GET['user']) && in_array($_GET['op'], array('assign', 'unassign'))) {
                        if ($node->$_GET['op']($_GET['user'])) {
                            // ok
                        } else {
                            Message::error(implode('<br />', $errors));
                        }
                    }

                    $node->admins = Model\Node::getAdmins($node->id);
                    $admins = Model\User::getAdmins(true);

                    return new View(
                                    'admin/index.html.php',
                                    array(
                                        'folder' => 'nodes',
                                        'file' => 'admins',
                                        'action' => 'admins',
                                        'node' => $node,
                                        'admins' => $admins
                                    )
                    );
                    break;
            }


            $nodes = Model\Node::getAll($filters);
            $status = array(
                'active' => 'Activo',
                'inactive' => 'Inactivo'
            );
            $admins = Model\Node::getAdmins();

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'nodes',
                    'file' => 'list',
                    'filters' => $filters,
                    'nodes' => $nodes,
                    'status' => $status,
                    'admins' => $admins
                )
            );
        }

    }

}
