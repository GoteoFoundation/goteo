<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Application\Message,
        Goteo\Model;

    class Patron {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            $errors = array();

            // guardar cambios en registro de apadrinamiento
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                // manteniendo el orden
                $order = Model\Patron::next($_POST['user'], $node);

                // objeto
                $promo = new Model\Patron(array(
                    'id' => $_POST['id'],
                    'node' => $node,
                    'project' => $_POST['project'],
                    'user' => $_POST['user'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'link' => $_POST['link'],
                    'order' => $order,
                    'active' => $_POST['active']
                ));

				if ($promo->save($errors)) {
                    if ($_POST['action'] == 'add') {
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
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Patron::setPending($promo->id, 'post')) {
                        Message::Error('NO se ha marcado como pendiente de traducir!');
                    }

                    throw new Redirection('/admin/patron/view/'.$_POST['user']);
                }
				else {
                    Message::Error('El registro no se ha grabado correctamente. '. implode(', ', $errors));
                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
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
                                'admin/index.html.php',
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

            // aplicar cambio de orden
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_order'])) {
                foreach ($_POST as $key=>$value) {
                    $parts = explode('_', $key);

                    if ($parts[0] == 'order') {
                        Model\Patron::setOrder($parts[1], $value);
                    }
                }
            }


            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Patron::setActive($id, $set);

                    if (isset($_GET['user']))
                        throw new Redirection('/admin/patron/view/'.$_GET['user']);
                    else
                        throw new Redirection('/admin/patron/');

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

                    } else {
                        Message::Error('No se ha podido quitar correctamente el apadrinamiento');
                    }

                    if (isset($_GET['user']))
                        throw new Redirection('/admin/patron/view/'.$_GET['user']);
                    else
                        throw new Redirection('/admin/patron/');

                    break;

                case 'add':

                    $user = (isset($_GET['user'])) ? (object) array('id'=>$_GET['user']) : null;

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'patron',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('user' => $user),
                            'available' => Model\Patron::available(null, $node)
                        )
                    );
                    break;

                case 'edit':
                    $promo = Model\Patron::get($id);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'patron',
                            'file' => 'edit',
                            'action' => 'edit',
                            'promo' => $promo,
                            'available' => Model\Patron::available($promo->project, $node),
                        )
                    );
                    break;

                case 'add_home':
                      if (Model\Patron::add_home($id)) {
                        throw new Redirection('/admin/patron');
                    }
                    break;

                 case 'remove_home':
                      if (Model\Patron::remove_home($id)) {
                        throw new Redirection('/admin/patron');
                    }
                    break;

                case 'reorder':
                    // promos by user
                    $patrons = array();
                    $patroned = Model\Patron::getAll($node);

                    foreach ($patroned as $promo) {
                        if (!isset($patrons[$promo->user->id])&&($promo->order)) {
                            $patrons[$promo->user->id] = (object) array(
                                'id' => $promo->user->id,
                                'name' => $promo->user->name,
                                'order' => $promo->order,
                                'home' => $promo->home
                            );
                        }
                    }

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'patron',
                            'file' => 'order',
                            'patrons' => $patrons
                        )
                    );
                    break;

                    case 'up':

                    Model\Patron::up($id);
                    throw new Redirection('/admin/patron/reorder');
                    break;

                case 'down':

                    Model\Patron::down($id);

                    throw new Redirection('/admin/patron/reorder');
                    break;

                case 'view':
                    // promos by user
                    $promos  = array();
                    $patrons = array();
                    $patroned = Model\Patron::getAll($node);

                    foreach ($patroned as $promo) {
                        if (!isset($patrons[$promo->user->id])) {
                            $patrons[$promo->user->id] = (object) array(
                                'id' => $promo->user->id,
                                'name' => $promo->user->name,
                                'order' => $promo->order
                            );
                        }
                        $promos[$promo->user->id][] = $promo;
                    }

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'patron',
                            'file' => 'view',
                            'patron' => $patrons[$id],
                            'promos' => $promos[$id]
                        )
                    );
                    break;
            }

            // promos by user
            $patrons = array();
            $patroned = Model\Patron::getAll($node);

            foreach ($patroned as $promo) {
                if (!isset($patrons[$promo->user->id])) {
                    $patrons[$promo->user->id] = (object) array(
                        'id' => $promo->user->id,
                        'name' => $promo->user->name,
                        'order' => $promo->order,
                        'home' => $promo->home
                    );
                }
            }

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'patron',
                    'file' => 'list',
                    'patrons' => $patrons
                )
            );

        }

    }

}
