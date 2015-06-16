<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

class FooterSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $errors = array();

        if ($this->isPost() && $this->getPost('action') === 'add') {

            // objeto
            $post = new Model\Post(array(
                'id' => $this->getPost('post'),
                'order' => $this->getPost('order'),
                'footer' => $this->getPost('footer')
            ));

			if ($post->update($errors)) {
                Message::info('Entrada colocada en el footer correctamente');
			}
			else {
                Message::error(implode('<br />', $errors));

                return array(
                        'folder' => 'footer',
                        'file' => 'add',
                        'action' => 'add',
                        'post' => $post
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

                return array(
                        'folder' => 'footer',
                        'file' => 'add',
                        'action' => 'add',
                        'post' => (object) array('order' => $next)
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

        return array(
                'folder' => 'footer',
                'file' => 'list',
                'posts' => $posts
        );

    }

}
