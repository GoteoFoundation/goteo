<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Model;

    class Nodes {

        public static function process ($action = 'list', $id = null) {

            $filters = array();
            $fields = array('status', 'admin', 'name');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $node = new Model\Node(array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'admin' => $_POST['admin'],
                    'active' => $_POST['active']
                ));

				if ($node->create($errors)) {

                    if ($_POST['action'] == 'add') {
						$success[] = 'Nodo creado';
                        $txt_log = 'creado';
                    } else {
						$success[] = 'Nodo actualizado';
                        $txt_log = 'actualizado';
					}

                    // Evento feed
                    $log = new Feed();
                    $log->populate('Nodo gestionado desde admin', 'admin/nodes',
                        \vsprintf('El admin %s ha %s el Nodo %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', $txt_log),
                            Feed::item('project', $_POST['name']))
                        ));
                    $log->doAdmin('admin');
                    unset($log);

				}
				else {
                    switch ($_POST['action']) {
                        case 'add':
							$errors[] = 'Fallo al crear, revisar los campos';

                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'nodes',
                                    'file' => 'add',
                                    'action' => 'add',
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
							$errors[] = 'Fallo al actualizar, revisar los campos';

                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'nodes',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'node' => $node,
                                    'errors' => $errors
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'add':
                    return new View(
                        'view/admin/index.html.php',
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
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'nodes',
                            'file' => 'edit',
                            'action' => 'edit',
                            'node' => $node
                        )
                    );
                    break;
            }


            $nodes = Model\Node::getAll($filters);
            $status = array(
                        'active' => 'Activo',
                        'inactive' => 'Inactivo'
                    );

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'nodes',
                    'file' => 'list',
                    'filters' => $filters,
                    'nodes' => $nodes,
                    'status' => $status,
                    'errors' => $errors,
                    'success' => $success
                )
            );
            
        }

    }

}
