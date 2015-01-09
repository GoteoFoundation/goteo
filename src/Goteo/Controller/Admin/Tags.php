<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Tags {

        public static function process ($action = 'list', $id = null) {

            $model = 'Goteo\Model\Blog\Post\Tag';
            $url = '/admin/tags';

            $errors = array();

            switch ($action) {
                case 'add':
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'base',
                            'file' => 'edit',
                            'data' => (object) array(),
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
                                        'label' => 'Tag',
                                        'name' => 'name',
                                        'type' => 'text'
                                    )
                                )

                            )
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        $errors = array();

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name']
                        ));

                        if ($item->save($errors)) {

                            // tratar si han marcado pendiente de traducir
                            if (isset($_POST['pending']) && $_POST['pending'] == 1
                                && !Model\Blog\Post\Tag::setPending($item->id, 'post')) {
                                Message::Error('NO se ha marcado como pendiente de traducir!');
                            }

                            throw new Redirection($url);
                        } else {
                            Message::Error(implode('<br />', $errors));
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
                                    'label' => Text::get('regular-save')
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Tag',
                                        'name' => 'name',
                                        'type' => 'text'
                                    )
                                )

                            )
                        )
                    );

                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
            }

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'model' => 'tag',
                    'addbutton' => 'Nuevo tag',
                    'data' => $model::getList(1),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Tag',
                        'used' => 'Entradas',
                        'translate' => '',
                        'remove' => ''
                    ),
                    'url' => "$url"
                )
            );

        }

    }

}
