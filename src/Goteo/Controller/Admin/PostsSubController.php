<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

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


    public function upAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('up', $id, $this->getFilters(), $subaction));
    }

    public function downAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('down', $id, $this->getFilters(), $subaction));
    }

    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }

    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }

    public function removeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove', $id, $this->getFilters(), $subaction));
    }

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


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

