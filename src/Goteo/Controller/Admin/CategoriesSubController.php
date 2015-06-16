<?php

namespace Goteo\Controller\Admin;

class CategoriesSubController extends AbstractSubController {

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

