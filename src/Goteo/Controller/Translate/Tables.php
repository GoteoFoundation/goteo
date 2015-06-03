<?php

namespace Goteo\Controller\Translate {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Application\Message,
        Goteo\Library\Content,
        Goteo\Library\Text,
        Goteo\Application\Lang;

    class Tables
    {

        public static function process($table = null, $action = 'list', $id = null, &$errors = array())
        {

            // comprobamos los filtros
            $filters = array();
            $fields = array('type', 'text', 'pending');
            if (!isset($_GET['pending'])) $_GET['pending'] = 0;
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                    $_SESSION['translate_filters']['tables'][$field] = (string)$_GET[$field];
                } elseif (!empty($_SESSION['translate_filters']['tables'][$field])) {
                    // si no lo tenemos en el get, cogemos de la sesion pero no lo pisamos
                    $filters[$field] = $_SESSION['translate_filters']['tables'][$field];
                }
            }
            $filters['table'] = $table;

            $filter = "?type={$filters['type']}&text={$filters['text']}&pending={$filters['pending']}";

            // si llega post, vamos a guardar los cambios
            if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                if (!in_array($table, \array_keys(Content::$tables))) {
                    Message::Error("Tabla $table desconocida");
                    if (isset($_SESSION['translate_node']))
                        throw new Redirection('/dashboard/translates');
                    else
                        throw new Redirection('/translate');
                }

                if (Content::save($_POST, $errors)) {

                    // Evento Feed
                    /*
                    $log = new Feed();
                    $log->populate('contenido traducido (traductor)', '/translate/'.$table,
                        \vsprintf('El traductor %s ha %s el contenido del registro %s de la tabla %s al %s', array(
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                        Feed::item('relevant', 'Traducido'),
                        Feed::item('blog', $id),
                        Feed::item('blog', $table),
                        Feed::item('relevant', Lang::get($_SESSION['translate_lang'])->name)
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                    */

                    Message::Info('Contenido del registro <strong>' . $id . '</strong> de la tabla <strong>' . $table . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');

                    if (isset($_SESSION['translate_node'])) {
                        throw new Redirection('/dashboard/translates/' . $table . 's');
                    }

                    throw new Redirection("/translate/$table/$filter&page=" . $_GET['page']);
                } else {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($_SESSION['user']->id, 'user');
                    $log->populate('contenido traducido (traductor)', '/translate/' . $table,
                        \vsprintf('El traductor %s le ha %s el contenido del registro %s de la tabla %s al %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Fallado al traducir'),
                            Feed::item('blog', $id),
                            Feed::item('blog', $table),
                            Feed::item('relevant', Lang::get($_SESSION['translate_lang'])->name)
                        )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::Error('Ha habido algun ERROR al traducir el contenido del registro <strong>' . $id . '</strong> de la tabla <strong>' . $table . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                }
            }

            // lista
            if ($action == 'list') {
                // contamos el número de palabras
                $nwords = 0;
                $data = Content::getAll($filters, $_SESSION['translate_lang']);

                //recolocamos los post para la paginacion
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

                // valores de filtro
                $types = Content::$fields[$table]; // por tipo de campo
            }

            // edición
            if ($action == 'edit') {

                $content = Content::get($table, $id, $_SESSION['translate_lang']);

            }


            return new View(
                'translate/index.html.php',
                array(
                    'section' => 'tables',
                    'action' => $action,
                    'table' => $table,
                    'id' => $id,
                    'content' => $content,
                    'list' => $list,
                    'types' => $types,
                    'nwords' => $nwords,
                    'filter' => $filter,
                    'filters' => $filters,
                    'errors' => $errors
                )
            );

        }
    }
}
