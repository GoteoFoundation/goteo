<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
		Goteo\Library\Page;

    class Pages {

        static public $node_pages = array('about', 'contact', 'press', 'service');


        public static function process ($action = 'list', $id = null) {

            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;

            $errors = array();

            switch ($action) {
                case 'add':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $page = new Page();
                        $page->id = $_POST['id'];
                        $page->name = $_POST['name'];
                        if ($page->add($errors)) {

                            Message::info('La página <strong>'.$page->name. '</strong> se ha creado correctamente, se puede editar ahora.');

                            throw new Redirection("/admin/pages/edit/{$page->id}");
                        } else {
                            Message::error('No se ha creado bien '. implode('<br />', $errors));
                            throw new Redirection("/admin/pages/add");
                        }
                    }

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'add'
                        )
                     );
                    break;

                case 'edit':
                    if ($node != \GOTEO_NODE && !in_array($id, self::$node_pages)) {
                        Message::info('No puedes gestionar la página <strong>'.$id.'</strong>');
                        throw new Redirection("/admin/pages");
                    }
                    // si estamos editando una página
                    $page = Page::get($id, $node, \GOTEO_DEFAULT_LANG);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $page->name = $_POST['name'];
                        $page->description = $_POST['description'];
                        $page->content = $_POST['content'];
                        if ($page->save($errors)) {

                            // Evento Feed
                            $log = new Feed();
                            if ($node != \GOTEO_NODE && in_array($id, self::$node_pages)) {
                                $log->setTarget($node, 'node');
                            }
                            $log->populate('modificacion de página institucional (admin)', '/admin/pages',
                                \vsprintf("El admin %s ha %s la página institucional %s", array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Modificado'),
                                Feed::item('relevant', $page->name, $page->url)
                            )));
                            $log->doAdmin('admin');
                            unset($log);

                            Message::info('La página '.$page->name. ' se ha actualizado correctamente');

                            // tratar si han marcado pendiente de traducir
                            // no usamos Core\Model porque no es tabla _lang
                            if (isset($_POST['pending']) && $_POST['pending'] == 1) {
                                $ok = Page::setPending($id, $node, $errors);
                                if (!$ok) {
                                    Message::error(implode('<br />', $errors));
                                }
                            }

                            throw new Redirection("/admin/pages");
                        } else {
                            Message::error(implode('<br />', $errors));
                        }
                    }


                    // sino, mostramos para editar
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'edit',
                            'page' => $page
                        )
                     );
                    break;

                case 'list':
                    // si estamos en la lista de páginas
                    $pages = Page::getList($node);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'list',
                            'pages' => $pages,
                            'node' => $node
                        )
                    );
                    break;
            }

        }

    }

}
