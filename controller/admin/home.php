<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Home {

        public static function process ($action = 'list', $id = null, $type = 'main') {

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
            if ($node == \GOTEO_NODE) {
                $type = 'main';
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $item = new Model\Home(array(
                    'item' => $_POST['item'],
                    'type' => $_POST['type'],
                    'node' => $node,
                    'order' => $_POST['order'],
                    'move' => 'down'
                ));

				if ($item->save($errors)) {
                    Message::Info('Elemento añadido correctamente');
				} else {
                    Message::Error(implode('<br />', $errors));
                }
			}


            switch ($action) {
                case 'remove':
                    Model\Home::delete($id, $node, $type);
                    throw new Redirection('/admin/home');
                    break;
                case 'up':
                    Model\Home::up($id, $node, $type);
                    throw new Redirection('/admin/home');
                    break;
                case 'down':
                    Model\Home::down($id, $node, $type);
                    throw new Redirection('/admin/home');
                    break;
                case 'add':
                    $next = Model\Home::next($node, 'main');
                    $availables = Model\Home::available($node);

                    if (empty($availables)) {
                        Message::Info('Todos los elementos disponibles ya estan en portada');
                        throw new Redirection('/admin/home');
                        break;
                    }
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'home',
                            'file' => 'add',
                            'action' => 'add',
                            'home' => (object) array('node'=>$node, 'order'=>$next, 'type'=>'main'),
                            'availables' => $availables
                        )
                    );
                    break;
                case 'addside':
                    $next = Model\Home::next($node, 'side');
                    $availables = Model\Home::availableSide($node);

                    if (empty($availables)) {
                        Message::Info('Todos los elementos laterales disponibles ya estan en portada');
                        throw new Redirection('/admin/home');
                        break;
                    }
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'home',
                            'file' => 'add',
                            'action' => 'add',
                            'home' => (object) array('node'=>$node, 'order'=>$next, 'type'=>'side'),
                            'availables' => $availables
                        )
                    );
                    break;
            }

            $items = Model\Home::getAll($node);
            $side_items = Model\Home::getAllSide($node);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'home',
                    'file' => 'list',
                    'items' => $items,
                    'side_items' => $side_items
                )
            );
            
        }

    }

}
