<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
		Goteo\Library\Page;

    class Pages {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'edit':
                    // si estamos editando una página
                    $page = Page::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $page->name = $_POST['name'];
                        $page->description = $_POST['description'];
                        $page->content = $_POST['content'];
                        if ($page->save($errors)) {

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('modificacion de página institucional (admin)', '/admin/pages',
                                \vsprintf("El admin %s ha %s la página institucional %s", array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Modificado'),
                                Feed::item('relevant', $page->name, $page->url)
                            )));
                            $log->doAdmin('admin');
                            unset($log);

                            throw new Redirection("/admin/pages");
                        }
                    }


                    // sino, mostramos para editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'edit',
                            'page' => $page,
                            'errors'=>$errors
                        )
                     );
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $pages = Page::getAll();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'pages',
                            'file' => 'list',
                            'pages' => $pages
                        )
                    );
                    break;
            }

        }

    }

}
