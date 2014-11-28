<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Library\Message,
        Goteo\Model;

    class Footer {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add') {

                // objeto
                $post = new Model\Post(array(
                    'id' => $_POST['post'],
                    'order' => $_POST['order'],
                    'footer' => $_POST['footer']
                ));

				if ($post->update($errors)) {
                    Message::Info('Entrada colocada en el footer correctamente');
				}
				else {
                    Message::Error(implode('<br />', $errors));

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'footer',
                            'file' => 'add',
                            'action' => 'add',
                            'post' => $post
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Post::up($id, 'footer');
                    break;
                case 'down':
                    Model\Post::down($id, 'footer');
                    break;
                case 'add':
                    // siguiente orden
                    $next = Model\Post::next('footer');

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'footer',
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
                    Model\Post::remove($id, 'footer');
                    break;
            }

            $posts = Model\Post::getAll('footer');

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'footer',
                    'file' => 'list',
                    'posts' => $posts
                )
            );

        }

    }

}
