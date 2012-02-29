<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Model;

    class Home {

        public static function process ($action = 'list', $id = null) {

            $node = $_SESSION['admin_node'];

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $item = new Model\Home(array(
                    'item' => $_POST['item'],
                    'node' => $node,
                    'order' => $_POST['order'],
                    'move' => 'down'
                ));

				if ($item->save($errors)) {
                    $success[] = 'Elemento añadido correctamente';
				}
			}


            switch ($action) {
                case 'up':
                    Model\Home::up($id, $node);
                    break;
                case 'down':
                    Model\Home::down($id, $node);
                    break;
                case 'add':
                    $next = Model\Home::next($node);
                    $availables = Model\Home::available($node);

                    if (empty($availables)) {
                        $errors[] = 'Todos los elementos disponibles ya estan en la portada';
                        break;
                    }
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'home',
                            'file' => 'add',
                            'action' => 'add',
                            'home' => (object) array('node' => $node, 'order' => $next),
                            'availables' => $availables
                        )
                    );
                    break;
                case 'remove':
                    Model\Home::delete($id, $node);
                    break;
            }

            $items = Model\Home::getAll($node);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'home',
                    'file' => 'list',
                    'items' => $items,
                    'errors' => $errors,
                    'success' => $success
                )
            );
            
        }

    }

}
