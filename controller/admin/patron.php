<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Patron {

        public static function process ($action = 'list', $id = null, $flag = null) {

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
            
            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                // objeto
                $promo = new Model\Patron(array(
                    'id' => $_POST['id'],
                    'node' => $node,
                    'project' => $_POST['project'],
                    'user' => $_POST['user'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'link' => $_POST['link'],
                    'order' => $_POST['order'],
                    'active' => $_POST['active']
                ));

				if ($promo->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            Message::Info('Proyecto apadrinado correctamente');

                            $projectData = Model\Project::getMini($_POST['project']);
                            $userData = Model\User::getMini($_POST['user']);

                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($projectData->id);
                            $log->populate('nuevo proyecto apadrinado (admin)', '/admin/patron',
                                \vsprintf('El admin %s ha hecho al usuario %s padrino del proyecto %s', array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('project', $projectData->name, $projectData->id)
                            )));
                            $log->doAdmin('admin');
                            unset($log);

                            break;
                        case 'edit':
                            Message::Info('Apadrinamiento actualizado correctamente');
                            break;
                    }
				}
				else {
                    Message::Error('El registro no se ha grabado correctamente. '. implode(', ', $errors));
                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'patron',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'promo' => $promo,
                                    'available' => Model\Patron::available(null, $node),
                                    'status' => $status
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'patron',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'promo' => $promo,
                                    'available' => Model\Patron::available($promo->project, $node),
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Patron::setActive($id, $set);
                    throw new Redirection('/admin/patron');
                    break;
                case 'up':
                    Model\Patron::up($id, $node);
                    throw new Redirection('/admin/patron');
                    break;
                case 'down':
                    Model\Patron::down($id, $node);
                    throw new Redirection('/admin/patron');
                    break;
                case 'remove':
                    if (Model\Patron::delete($id)) {
                        $projectData = Model\Project::getMini($id);

                        // Evento Feed
                        $log = new Feed();
                        $log->setTarget($projectData->id);
                        $log->populate('proyecto desapadrinado (admin)', '/admin/promote',
                            \vsprintf('El admin %s ha %s del proyecto %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Quitado el apadrinamiento'),
                            Feed::item('project', $projectData->name, $projectData->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                        Message::Info('Apadrinamiento quitado correctamente');
                    } else {
                        Message::Error('No se ha podido quitar correctamente el apadrinamiento');
                    }
                    throw new Redirection('/admin/patron');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Patron::next($node);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'patron',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next),
                            'available' => Model\Patron::available(null, $node),
                            'status' => $status
                        )
                    );
                    break;
                case 'edit':
                    $promo = Model\Patron::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'patron',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo,
                            'available' => Model\Patron::available($promo->project, $node),
                        )
                    );
                    break;
            }


            $patroned = Model\Patron::getAll($node);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'patron',
                    'file' => 'list',
                    'patroned' => $patroned
                )
            );
            
        }

    }

}
