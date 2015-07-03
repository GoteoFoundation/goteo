<?php

namespace Goteo\Controller\Translate {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Page,
        Goteo\Library\Text,
        Goteo\Application\Message,
        Goteo\Application\Session,
        Goteo\Application\Lang;

    class Pages
    {

        // this method is to process node translating its own pages from dashboard,
        // regular page translation are handled by Translate\Tables
        public static function process($action = 'list', $id = null, &$errors = array())
        {

            // comprobamos los filtros
            $filters = array();
            $fields = array('text', 'pending');
            if (!isset($_GET['pending'])) $_GET['pending'] = 0;
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                    $_SESSION['translate_filters']['texts'][$field] = (string)$_GET[$field];
                } elseif (!empty($_SESSION['translate_filters']['texts'][$field])) {
                    // si no lo tenemos en el get, cogemos de la sesion pero no lo pisamos
                    $filters[$field] = $_SESSION['translate_filters']['texts'][$field];
                }
            }

            $filter = "?text={$filters['text']}&pending={$filters['pending']}";

            // si llega post, vamos a guardar los cambios
            if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
                if (Page::update($id, $_POST['lang'], $_POST['node'], $_POST['name'], $_POST['description'], $_POST['content'], $errors)) {

                    // Evento Feed
                    /*
                    $log = new Feed();
                    $log->populate('pagina traducida (traductor)', '/translate/pages',
                        \vsprintf('El traductor %s ha %s la página %s del nodo %s al %s', array(
                        Feed::item('user', Session::getUser()->name, Session::getUserId()),
                        Feed::item('relevant', 'Traducido'),
                        Feed::item('blog', $id),
                        Feed::item('blog', $_POST['node']),
                        Feed::item('relevant', Lang::get($_POST['lang'])->name)
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                    */

                    Message::info('Contenido de la Pagina <strong>' . $id . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');

                    if ($_POST['node'] != \GOTEO_NODE) {
                        if (isset($_SESSION['translate_node'])) {
                            throw new Redirection('/dashboard/translates/pages');
                        } else {
                            throw new Redirection('/admin/pages');
                        }
                    }

                    throw new Redirection("/translate/pages");
                } else {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget(Session::getUserId(), 'user');
                    $log->populate('pagina traducida (traductor)', '/translate/pages',
                        \vsprintf('Al traductor %s le ha %s la página %s del nodo %s al %s', array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('relevant', 'Fallado al traducir'),
                            Feed::item('blog', $id),
                            Feed::item('blog', $_POST['node']),
                            Feed::item('relevant', Lang::get($_POST['lang'])->name)
                        )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::error('Ha habido algun ERROR al traducir el contenido de la pagina <strong>' . $id . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                }
            }


            // lista
            if ($action == 'list') {
                // contamos el número de palabras
                $nwords = 0;

                $pages = Page::getAll($filters, $_SESSION['translate_lang']);

                //recolocamos los post para la paginacion
                foreach ($pages as $page) {
                    // si es pendiente contamos las palabras del original
                    if ($page->pendiente) {
                        $nwords += Text::wcount($page->original_name);
                        $nwords += Text::wcount($page->original_description);
                        $nwords += Text::wcount($page->original_content);
                    }
                    else {
                        $nwords += Text::wcount($page->name);
                        $nwords += Text::wcount($page->description);
                        $nwords += Text::wcount($page->content);
                    }

                }
            }

            // edición
            if ($action == 'edit') {

                if (isset($_SESSION['translate_node'])) {
                    if (is_object($_SESSION['translate_node'])) {
                        $node = $_SESSION['translate_node']->id;
                    } else {
                        $node = $_SESSION['translate_node'];
                    }
                } else {
                    $node = \GOTEO_NODE;
                }


                $page = Page::get($id, $node, $_SESSION['translate_lang']);
                $original = Page::get($id, $node, Lang::getDefault());


            }

            return new View(
                'translate/index.html.php',
                array(
                    'section' => 'pages',
                    'action' => $action,
                    'pages' => $pages,
                    'id' => $id,
                    'node' => $node,
                    'page' => $page,
                    'original' => $original,
                    'nwords' => $nwords,
                    'filter' => $filter,
                    'filters' => $filters,
                    'errors' => $errors
                )
            );

        }
    }
}
