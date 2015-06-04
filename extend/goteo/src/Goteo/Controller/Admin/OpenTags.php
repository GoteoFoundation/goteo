<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Application\Message,
        Goteo\Model;

    class OpenTags {

        public static function process ($action = 'list', $id = null) {

            $model = 'Goteo\Model\OpenTag';
            $url = '/admin/open_tags';

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    $el_item = $_POST['item'];
                    error_log($el_item);
                    if (!empty($el_item)) {
                        $post = $el_item;
                    } else {
                        $post = null;
                    }

                    // objeto
                    $open_tag = new Model\OpenTag(array(
                        'id' => $_POST['id'],
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'order' => $_POST['order'],
                        'post' => $post
                    ));

                    if ($open_tag->save($errors)) {
                        Message::info('Datos guardados');

                        // tratar si han marcado pendiente de traducir
                        if (isset($_POST['pending']) && $_POST['pending'] == 1
                            && !Model\OpenTag::setPending($open_tag->id, 'post')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        throw new Redirection('/admin/open_tags');
                    }

                    else {
                    Message::error(implode('<br />', $errors));

                    // otros elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    switch ($_POST['action']) {
                        case 'add':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'open_tags',
                                    'file' => 'edit',
                                    'action' => 'add',
                                    'open_tag' => $open_tag,
                                    'items' => $items,
                                    'autocomplete' => true
                                )
                            );
                            break;
                        case 'edit':
                            return new View(
                                'admin/index.html.php',
                                array(
                                    'folder' => 'open_tags',
                                    'file' => 'edit',
                                    'action' => 'edit',
                                    'story' => $open_tag,
                                    'items' => $items,
                                    'autocomplete' => true
                                )
                            );
                            break;
                    }
                }
            }




            switch ($action) {

                case 'edit':

                    $open_tag = Model\OpenTag::get($id);
                        // elementos disponibles
                        $items = Model\Post::getAutocomplete();

                        return new View(
                            'admin/index.html.php',
                            array(
                                'folder' => 'open_tags',
                                'file' => 'edit',
                                'action' => 'edit',
                                'open_tag' => $open_tag,
                                'items' => $items,
                                'autocomplete' => true
                            )
                        );

                    break;

                case 'add':
                    // siguiente orden
                    $next = Model\OpenTag::next();
                    // elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'open_tags',
                            'file' => 'edit',
                            'action' => 'add',
                            'open_tag' => (object) array('order' => $next),
                            'items' => $items,
                            'autocomplete' => true
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
            }

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'base',
                    'file' => 'list',
                    'model' => 'open_tag',
                    'addbutton' => 'Nueva agrupación',
                    'data' => $model::getAll(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Agrupación',
                        'numProj' => 'Proyectos',
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
