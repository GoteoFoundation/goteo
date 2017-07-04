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
 * Gestion de categorias
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Model\Image;
use Goteo\Model\SocialCommitment;

class CategoriesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'categories-lb-list',
      'add' => 'categories-lb-add',
      'edit' => 'categories-lb-edit',
      'translate' => 'categories-lb-translate',
      'keywords' => 'categories-lb-keywords',
    );


    static protected $label = 'categories-lb';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function keywordsAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('keywords', $id, $this->getFilters(), $subaction));
    }


    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }

    public function removeAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('remove', $id, $this->getFilters(), $subaction));
    }

    public function upAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('up', $id, $this->getFilters(), $subaction));
    }

    public function downAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('down', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null) {

        $model = 'Goteo\Model\Category';
        $url = '/admin/categories';

        // Prepare shperes of call
        $social_commitments= SocialCommitment::getall();

        $social_commitments_normalize=[];

        foreach($social_commitments as $social)
        {
            $social_commitments_normalize[$social->id]=$social->name;
        }

        $errors = array();

        switch ($action) {
            case 'add':
                if ($this->hasGet('word')) {
                    $item = (object) array('name'=>$this->getGet('word'));
                } else {
                    $item = (object) array();
                }
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit",
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Añadir'
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Categoría',
                                    'name' => 'name',
                                    'type' => 'text'
                                ),
                                'description' => array(
                                    'label' => 'Descripción',
                                    'name' => 'description',
                                    'type' => 'textarea',
                                    'properties' => 'cols="100" rows="2"',

                                ),
                                 'social_commitment' => array(
                                    'label' => 'Compromiso Social',
                                    'name' => 'social_commitment',
                                    'type' => 'select',
                                    'options' => $social_commitments_normalize
                                    )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    $active= $this->getPost('active') ? 1 : 0;

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name'),
                        'image' => $this->getPost('image'),
                        'description' => $this->getPost('description'),
                        'active'    => $active,
                        'social_commitment' => $this->getPost('social_commitment')
                    ));

                    // tratar si quitan la imagen
                    if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                        $image = Image::get($item->image);
                        $image->remove($errors);
                        $item->image = null;
                        $removed = true;
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image']['name'])) {
                        $item->image = $_FILES['image'];
                    }

                    if ($item->save($errors)) {
                        return $this->redirect($url);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $item = $model::get($id, Config::get('lang'));
                }

                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit" . ($id ? "/$id" : ''),
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Guardar'
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Categoría',
                                    'name' => 'name',
                                    'type' => 'text'
                                ),
                                'description' => array(
                                    'label' => 'Descripción',
                                    'name' => 'description',
                                    'type' => 'textarea',
                                    'properties' => 'cols="100" rows="2"',

                                ),
                                 'social_commitment' => array(
                                    'label' => 'Compromiso Social',
                                    'name' => 'social_commitment',
                                    'type' => 'select',
                                    'options' => $social_commitments_normalize
                                    )
                            )
                    )
                );

                break;
            case 'up':
                $model::up($id);
                break;
            case 'down':
                $model::down($id);
                break;
            case 'remove':
                if ($model::delete($id)) {
                    return $this->redirect($url);
                }
                break;
            case 'keywords':

                return array(
                        'folder' => 'keywords',
                        'file' => 'list',
                        'categories' => $model::getList(),
                        'words' => $model::getKeyWords()
                );

                break;
        }

        return array(
                'folder' => 'base',
                'file' => 'list',
                'model' => 'category',
                'addbutton' => 'Nueva categoría',
                'otherbutton' => '<a href="/admin/categories/keywords" class="button">Ver Palabras clave</a>',
                'data' => $model::getAll(),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Categoría',
                    'numProj' => 'Proyectos',
                    'numUser' => 'Usuarios',
                    'order' => 'Prioridad',
                    'translate' => '',
                    'up' => '',
                    'down' => '',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}

