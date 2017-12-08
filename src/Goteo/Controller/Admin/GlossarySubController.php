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
 * Gestion del glosario
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message,
    Goteo\Model;

class GlossarySubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'glossary-lb-list',
      'edit' => 'glossary-lb-edit',
      'translate' => 'glossary-lb-translate',
    );


    static protected $label = 'glossary-lb';


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
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

        $url = '/admin/glossary';

		if ($this->isPost()) {

                $editing = false;

                if ($this->getPost('id')) {
                    $post = Model\Glossary::get($this->getPost('id'), Config::get('lang'));
                } else {
                    $post = new Model\Glossary();
                }

                // campos que actualizamos
                $fields = array(
                    'id',
                    'title',
                    'text',
                    'media',
                    'legend'
                );

                foreach ($fields as $field) {
                    $post->$field = $this->getPost($field);
                }

                // tratar la imagen y ponerla en la propiedad image
                if(!empty($_FILES['image_upload']['name'])) {
                    $post->image = $_FILES['image_upload'];
                    $editing = true;
                }

                // tratar las imagenes que quitan
                foreach ($post->gallery as $key=>$image) {
                    if ($this->getPost("gallery-{$image->hash}-remove")) {
                        $image->remove($errors, 'glossary');
                        $editing = true;
                    }
                }

                if (!empty($post->media)) {
                    $post->media = new Model\Project\Media($post->media);
                }

                /// este es el único save que se lanza desde un metodo process_
                if ($post->save($errors)) {
                    if ($action == 'edit') {
                        Message::info('El término se ha actualizado correctamente');
                    } else {
                        Message::info('Se ha añadido un nuevo término');
                        $id = $post->id;
                    }
                    $action = $editing ? 'edit' : 'list';

                    // tratar si han marcado pendiente de traducir
                    if ($this->hasPost('pending') && $this->getPost('pending') == 1
                        && !Model\Glossary::setPending($post->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                } else {
                    Message::error(implode('<br />', $errors));
                    Message::error('Ha habido algun problema al guardar los datos');
                }
        }

        switch ($action)  {
            case 'remove':
                // eliminar un término
                if (Model\Glossary::delete($id)) {
                    Message::info('Término eliminado');
                } else {
                    Message::error('No se ha podido eliminar el término');
                }
                break;
            case 'add':
                // nueva entrada con wisiwig
                // obtenemos datos basicos
                $post = new Model\Glossary();

                $message = 'Añadiendo un nuevo término';

                return array(
                        'folder' => 'glossary',
                        'file' => 'edit',
                        'action' => 'add',
                        'post' => $post,
                        'message' => $message
                );
                break;
            case 'edit':
                if (empty($id)) {
                    return $this->redirect('/admin/glossary');
                    break;
                } else {
                    $post = Model\Glossary::get($id, Config::get('lang'));

                    if (!$post instanceof Model\Glossary) {
                        Message::error('La entrada esta corrupta, contacte con nosotros.');
                        $action = 'list';
                        break;
                    }
                }

                $message = 'Editando un término existente';

                return array(
                        'folder' => 'glossary',
                        'file' => 'edit',
                        'action' => 'edit',
                        'post' => $post,
                        'message' => $message
                );
                break;
        }

        // lista de términos
        $posts = Model\Glossary::getAll();

        return array(
                'folder' => 'glossary',
                'file' => 'list',
                'posts' => $posts
        );

    }

}

