<?php
/**
 * Gestion de categorias
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;

class CategoriesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nueva Categoría',
      'edit' => 'Editando Categoría',
      'translate' => 'Traduciendo Categoría',
      'keywords' => 'Palabras clave',
    );


    static protected $label = 'Categorías';

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
                            'action' => "$url/edit/",
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

                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name'),
                        'description' => $this->getPost('description')
                    ));

                    if ($item->save($errors)) {
                        return $this->redirect($url);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $item = $model::get($id);
                }

                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit/$id",
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

