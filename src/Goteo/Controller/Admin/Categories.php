<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error;

    class Categories {

        public static function process ($action = 'list', $id = null) {

            $model = 'Goteo\Model\Category';
            $url = '/admin/categories';

            $errors = array();

            switch ($action) {
                case 'add':
                    if (isset($_GET['word'])) {
                        $item = (object) array('name'=>$_GET['word']);
                    } else {
                        $item = (object) array();
                    }
                    return new View(
                        'admin/index.html.php',
                        array(
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
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description']
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        } else {
                            Message::error(implode('<br />', $errors));
                        }
                    } else {
                        $item = $model::get($id);
                    }

                    return new View(
                        'admin/index.html.php',
                        array(
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
                        throw new Redirection($url);
                    }
                    break;
                case 'keywords':

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'keywords',
                            'file' => 'list',
                            'categories' => $model::getList(),
                            'words' => $model::getKeyWords()
                        )
                    );

                    break;
            }

            return new View(
                'admin/index.html.php',
                array(
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
                )
            );

        }

    }

}
