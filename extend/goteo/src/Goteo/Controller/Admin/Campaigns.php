<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Application\Message,
        Goteo\Model;

    class Campaigns {

        public static function process ($action = 'list', $id = null, $filters = array(), $flag = null) {

            $status = Model\Call::status();

            $errors = array();

            // solo para nodos
            if (!isset($_SESSION['admin_node'])) {
                throw new Redirection('/admin');
            }

            $node = $_SESSION['admin_node'];

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $campaign = new Model\Campaign(array(
                    'id' => $id,
                    'node' => $node,
                    'call' => $_POST['call'],
                    'order' => $_POST['order'],
                    'active' => $_POST['active']
                ));

				if ($campaign->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            Message::Info('Convocatoria destacada correctamente');

                            // parece que no se usa
                            // $callData = Model\Call::getMini($_POST['call']);

                            break;
                        case 'edit':
                            Message::Info('Destacado actualizado correctamente');
                            break;
                    }
				}
				else {

                    Message::Error(implode(', ', $errors));

                    // Convocatorias disponibles
                    $calls = Model\campaign::available($campaign->call, $node);


                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'campaigns',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'campaign' => $campaign,
                                    'status' => $status,
                                    'calls' => $calls
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'campaigns',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'campaign' => $campaign,
                                    'status' => $status,
                                    'calls' => $calls
                                )
                            );
                            break;
                    }
				}
			}

            switch ($action) {
                case 'active':
                    $set = $flag == 'on' ? true : false;
                    Model\Campaign::setActive($id, $set);
                    throw new Redirection('/admin/campaigns');
                    break;
                case 'up':
                    Model\Campaign::up($id, $node);
                    throw new Redirection('/admin/campaigns');
                    break;
                case 'down':
                    Model\Campaign::down($id, $node);
                    throw new Redirection('/admin/campaigns');
                    break;
                case 'remove':
                    if (Model\Campaign::delete($id)) {
                        // ok
                    } else {
                        Message::Error('No se ha podido quitar la convocatoria');
                    }
                    throw new Redirection('/admin/campaigns');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Campaign::next($node);

                    // Convocatorias disponibles disponibles
                    $calls = Model\Campaign::available(null, $node);
                    if (empty($calls)) {
                        Message::Info('No hay convocatorias disponibles para destacar');
                        throw new Redirection('/admin/campaigns');
                    }

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'campaigns',
                            'file' => 'edit',
                            'action' => 'add',
                            'campaign' => (object) array('order' => $next, 'node'=>$node),
                            'status' => $status,
                            'calls' => $calls
                        )
                    );
                    break;
                case 'edit':
                    $campaign = Model\Campaign::get($id);
                    // Convocatorias disponibles
                    $calls = Model\Campaign::available($campaign->call, $node);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'campaigns',
                            'file' => 'edit',
                            'action' => 'edit',
                            'campaign' => $campaign,
                            'status' => $status,
                            'calls' => $calls
                        )
                    );
                    break;
            }


            $setted = Model\Campaign::getAll(false, $node);

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'campaigns',
                    'file' => 'list',
                    'setted' => $setted
                )
            );

        }

    }

}
