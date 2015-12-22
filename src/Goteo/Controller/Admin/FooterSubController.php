<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Gestion del footer
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
    Goteo\Model;

class FooterSubController extends AbstractSubController {

    static protected $label = 'footer-lb';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }

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
                return $this->redirect('/admin/blog');
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
