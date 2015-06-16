<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

/**
 * TODO: check this
 *
 *  Clase auxiliar para la gestión de entradas en portada
 *  Las entradas en la portada de nodo utilizan la clase auxiliar PostsHome
 */
class PostsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $errors = array();

        if ($this->isPost() && $this->getPost('action') === 'add') {

            // esto es para añadir una entrada en la portada
            // objeto
            $post = new Model\Post(array(
                'id' => $this->getPost('post'),
                'order' => $this->getPost('order'),
                'home' => $this->getPost('home')
            ));

			if ($post->update($errors)) {
                Message::info('Entrada colocada en la portada correctamente');
			} else {
                Message::error(implode('<br />', $errors));
                return array(
                        'folder' => 'posts',
                        'file' => 'add',
                        'action' => 'add',
                        'post' => $post
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

                return array(
                        'folder' => 'posts',
                        'file' => 'add',
                        'action' => 'add',
                        'post' => (object) array('order' => $next)
                );
                break;
            case 'edit':
                return $this->redirect('/admin/blog');
                break;
            case 'remove':
                // se quita de la portada solamente
                Model\Post::remove($id, 'home');
                break;
        }

        $posts = Model\Post::getAll('home');

        return array(
                'folder' => 'posts',
                'file' => 'list',
                'posts' => $posts
        );

    }

}

