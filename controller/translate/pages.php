<?php

namespace Goteo\Controller\Translate {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Library\Page,
        Goteo\Library\Lang;

    class Pages
    {

        // this method is to process node translating its own pages from dashboard,
        // regular page translation are handled by Translate\Tables
        public static function process($action = 'list', $id = null, &$errors = array())
        {

            // si llega post, vamos a guardar los cambios
            if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
                if (Page::update($id, $_POST['lang'], $_POST['node'], $_POST['name'], $_POST['description'], $_POST['content'], $errors)) {

                    // Evento Feed
                    /*
                    $log = new Feed();
                    $log->populate('pagina traducida (traductor)', '/translate/pages',
                        \vsprintf('El traductor %s ha %s la página %s del nodo %s al %s', array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('relevant', 'Traducido'),
                        Feed::item('blog', $id),
                        Feed::item('blog', $_POST['node']),
                        Feed::item('relevant', Lang::get($_POST['lang'])->name)
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                    */

                    Message::Info('Contenido de la Pagina <strong>' . $id . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');

                    if ($_POST['node'] != \GOTEO_NODE) {
                        throw new Redirection('/dashboard/translates/pages');
                    }

                    throw new Redirection("/translate/pages");
                } else {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($_SESSION['user']->id, 'user');
                    $log->populate('pagina traducida (traductor)', '/translate/pages',
                        \vsprintf('Al traductor %s le ha %s la página %s del nodo %s al %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Fallado al traducir'),
                            Feed::item('blog', $id),
                            Feed::item('blog', $_POST['node']),
                            Feed::item('relevant', Lang::get($_POST['lang'])->name)
                        )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::Error('Ha habido algun ERROR al traducir el contenido de la pagina <strong>' . $id . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                }
            }


            // lista
            if ($action == 'list') {
                // contamos el número de palabras
                $nwords = 0;

                $node = (empty($_SESSION['admin_node'])) ? \GOTEO_NODE : $_SESSION['admin_node'];
                $pages = Page::getAll($_SESSION['translate_lang'], $node);

                //recolocamos los post para la paginacion
                /*
                $list = array();
                foreach ($data['pending'] as $key => $item) {
                    $item->pendiente = 1;
                    $nwords += Text::wcount($item->original); // si es pendiente contamos las palabras del original
                    $list[] = $item;
                }

                foreach ($data['ready'] as $key => $item) {
                    $item->pendiente = 0;
                    $nwords += Text::wcount($item->value);
                    $list[] = $item;
                }
                */
            }

            // edición
            if ($action == 'edit') {

                $node = (empty($_SESSION['admin_node'])) ? \GOTEO_NODE : $_SESSION['admin_node'];

                if (isset($_SESSION['translate_node'])) {
                    if (is_object($_SESSION['translate_node'])) {
                        $node = $_SESSION['translate_node']->id;
                    } else {
                        $node = $_SESSION['translate_node'];
                    }
                }

                $page = Page::get($id, $node, $_SESSION['translate_lang']);
                $original = Page::get($id, $node, \GOTEO_DEFAULT_LANG);


            }

            return new View(
                'view/translate/index.html.php',
                array(
                    'section' => 'pages',
                    'action' => $action,
                    'pages' => $pages,
                    'id' => $id,
                    'node' => $node,
                    'page' => $page,
                    'original' => $original,
                    'errors' => $errors
                )
            );

        }
    }
}