<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    /**
     *  Clase auxiliar para la gestión de entradas en portada
     *  Las entradas en la portada de nodo utilizan la clase auxiliar PostsHome
     */
    class Posts {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add') {

                // esto es para añadir una entrada en la portada
                // objeto
                $post = new Model\Post(array(
                    'id' => $_POST['post'],
                    'order' => $_POST['order'],
                    'home' => $_POST['home']
                ));

				if ($post->update($errors)) {
                    Message::Info('Entrada colocada en la portada correctamente');
				} else {
                    Message::Error(implode('<br />', $errors));
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'posts',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => $post
                        )
                    );
				}
			}

            switch ($action) {
                case 'up':
                    Model\Post::up($id, 'home');
                    break;
                case 'down':
                    Model\Post::down($id, 'home');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Post::next('home');

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'posts',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => (object) array('order' => $next)
                        )
                    );
                    break;
                case 'edit':
                    throw new Redirection('/admin/blog');
                    break;
                case 'remove':
                    // se quita de la portada solamente
                    Model\Post::remove($id, 'home');
                    break;
            }

            $posts = Model\Post::getAll('home');

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'posts',
                    'file' => 'list',
                    'posts' => $posts
                )
            );

        }

    }

}
