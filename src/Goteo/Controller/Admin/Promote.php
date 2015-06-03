<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Application\Message,
        Goteo\Model;

    class Promote {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $errors = array();

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $el_item = $_POST['item'];
                error_log($el_item);
                if (!empty($el_item)) {
                    $_POST['project'] = $el_item;
                } else {
                    $_POST['project'] = null;
                }

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
                    if ($_POST['action'] == 'add') {
                        $projectData = Model\Project::getMini($_POST['project']);

                        if ($node == \GOTEO_NODE) {
                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($projectData->id);
                            $log->populate('nuevo proyecto destacado en portada (admin)', '/admin/promote',
                                \vsprintf('El admin %s ha %s el proyecto %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', 'Destacado en portada', '/'),
                                    Feed::item('project', $projectData->name, $projectData->id)
                            )));
                            $log->doAdmin('admin');
                            unset($log);
                        }
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Promote::setPending($promo->id, 'post')) {
                        Message::Error('NO se ha marcado como pendiente de traducir!');
                    }

                    throw new Redirection('/admin/promote');
				}
				else {

                    Message::Error(implode(', ', $errors));

                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'promo' => $promo
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'promote',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'promo' => $promo
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
                    throw new Redirection('/admin/promote');
                    break;
                case 'up':
                    Model\Promote::up($id, $node);
                    throw new Redirection('/admin/promote');
                    break;
                case 'down':
                    Model\Promote::down($id, $node);
                    throw new Redirection('/admin/promote');
                    break;
                case 'remove':
                    if (Model\Promote::delete($id)) {
                        Message::Info('Destacado quitado correctamente');
                    } else {
                        Message::Error('No se ha podido quitar el destacado');
                    }
                    throw new Redirection('/admin/promote');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Promote::next($node);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next, 'node'=>$node),
                            'autocomplete' => true
                        )
                    );
                    break;
                case 'edit':
                    $promo = Model\Promote::get($id);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'promote',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo,
                            'autocomplete' => true
                        )
                    );
                    break;
            }


            $promoted = Model\Promote::getList(false, $node);
            // estados de proyectos
            $status = Model\Project::status();

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'promote',
                    'file' => 'list',
                    'promoted' => $promoted,
                    'status' => $status
                )
            );

        }

    }

}
