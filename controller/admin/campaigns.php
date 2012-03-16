<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Campaigns {

        public static function process ($action = 'list', $id = null, $flag = null) {

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
                            Message::Info('Campaña destacada correctamente');

                            $callData = Model\Call::getMini($_POST['call']);

                            break;
                        case 'edit':
                            Message::Info('Destacado actualizado correctamente');
                            break;
                    }
				}
				else {

                    Message::Error(implode(', ', $errors));

                    // campañas disponibles disponibles
                    $calls = Model\campaign::available($campaign->call, $node);


                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'campaign',
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
                                'view/admin/index.html.php',
                                array(
                                    'folder' => 'campaign',
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

                    break;
                case 'up':
                    Model\Campaign::up($id, $node);
                    break;
                case 'down':
                    Model\Campaign::down($id, $node);
                    break;
                case 'remove':
                    if (Model\Campaign::delete($id)) {
                        Message::Info('Campaña quitada correctamente');
                    } else {
                        Message::Error('No se ha podido quitar la campaña destacada');
                    }
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Campaign::next($node);

                    // campañas disponibles disponibles
                    $calls = Model\campaign::available(null, $node);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'campaign',
                            'file' => 'edit',
                            'action' => 'add',
                            'promo' => (object) array('order' => $next, 'node'=>$node),
                            'status' => $status,
                            'calls' => $calls
                        )
                    );
                    break;
                case 'edit':
                    $campaign = Model\Campaign::get($id);
                    // campañas disponibles disponibles
                    $calls = Model\campaign::available($campaign->call, $node);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'campaign',
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
                'view/admin/index.html.php',
                array(
                    'folder' => 'campaign',
                    'file' => 'list',
                    'setted' => $setted,
                            'status' => $status
                )
            );
            
        }

    }

}
