<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Promote {

        public static function process ($action = 'list', $id = null, $flag = null) {

            $errors = array();

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $promo = new Model\Promote(array(
                    'id' => $id,
                    'node' => $node,
                    'project' => $_POST['project'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'active' => $_POST['active']
                ));

				if ($promo->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            Message::Info('Proyecto destacado correctamente');

                            $projectData = Model\Project::getMini($_POST['project']);

                            if ($node == \GOTEO_NODE) {
                                // Evento Feed
                                $log = new Feed();
                                $log->populate('nuevo proyecto destacado en portada (admin)', '/admin/promote',
                                    \vsprintf('El admin %s ha %s el proyecto %s', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('relevant', 'Destacado en portada', '/'),
                                        Feed::item('project', $projectData->name, $projectData->id)
                                )));
                                $log->doAdmin('admin');
                                unset($log);
                            }

                            break;
                        case 'edit':
                            Message::Info('Destacado actualizado correctamente');
                            break;
                    }
				}
				else {
                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'promo' => $promo,
                                    'status' => $status,
                                    'errors' => $errors
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'promo' => $promo,
                                    'errors' => $errors
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Promote::setActive($id, $set);

                    /*
                    // Evento Feed
                    $log = new Feed();
                    $log_action = $set ? 'Mostrado en la portada' : 'Ocultado de la portada';
                    $log->populate('proyecto destacado mostrado/ocultado (admin)', '/admin/promote',
                        \vsprintf('El admin %s ha %s el proyecto %s', array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('relevant', $log_action),
                        Feed::item('project', $projectData->name, $projectData->id)
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                     * 
                     */

                    break;
                case 'up':
                    Model\Promote::up($id, $node);
                    break;
                case 'down':
                    Model\Promote::down($id, $node);
                    break;
                case 'remove':
                    if (Model\Promote::delete($id)) {
                        $projectData = Model\Project::getMini($id);

                        if ($node == \GOTEO_NODE) {
                            // Evento Feed
                            $log = new Feed();
                            $log->populate('proyecto quitado portada (admin)', '/admin/promote',
                                \vsprintf('El admin %s ha %s el proyecto %s', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Quitado de la portada'),
                                Feed::item('project', $projectData->name, $projectData->id)
                            )));
                            $log->doAdmin('admin');
                            unset($log);
                        }

                        Message::Info('Proyecto quitado correctamente');
                    } else {
                        Message::Error('No se ha podido quitar el destacado');
                    }
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Promote::next($node);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next, 'node'=>$node),
                            'status' => $status
                        )
                    );
                    break;
                case 'edit':
                    $promo = Model\Promote::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo
                        )
                    );
                    break;
            }


            $promoted = Model\Promote::getAll(false, $node);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'promote',
                    'file' => 'list',
                    'promoted' => $promoted,
                    'errors' => $errors,
                    'success' => $success
                )
            );
            
        }

    }

}
