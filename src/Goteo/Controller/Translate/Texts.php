<?php

namespace Goteo\Controller\Translate {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Library\Text,
        Goteo\Library\Lang;

    class Texts
    {

        public static function process($action = 'list', $id = null, &$errors = array())
        {
            // dont use cached texts
            define('GOTEO_ADMIN_NOCACHE', true);


            // comprobamos los filtros
            $filters = array();
            $fields = array('group', 'text', 'pending');
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

            $filter = "?group={$filters['group']}&text={$filters['text']}&pending={$filters['pending']}";

            // si llega post, vamos a guardar los cambios
            if ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {

                if (Text::save(array(
                    'id' => $id,
                    'text' => $_POST['text'],
                    'lang' => $_POST['lang']
                ), $errors)
                ) {

                    // Evento Feed
                    /*
                    $log = new Feed();
                    $log->populate('texto traducido (traductor)', '/translate/texts',
                        \vsprintf('El traductor %s ha %s el texto %s al %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Traducido'),
                            Feed::item('blog', $id),
                            Feed::item('relevant', Lang::get($_POST['lang'])->name)
                    )));
                    $log->doAdmin('admin');
                    unset($log);
                    */

                    Message::Info('Texto <strong>' . $id . '</strong> traducido correctamente al <strong>' . Lang::get($_POST['lang'])->name . '</strong>');

                    throw new Redirection("/translate/texts/$filter&page=" . $_GET['page']);
                } else {
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($_SESSION['user']->id, 'user');
                    $log->populate('texto traducido (traductor)', '/translate/texts',
                        \vsprintf('Al traductor %s  le ha %s el texto %s al %s', array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Fallado al traducir'),
                            Feed::item('blog', $id),
                            Feed::item('relevant', Lang::get($_POST['lang'])->name)
                        )));
                    $log->doAdmin('admin');
                    unset($log);

                    Message::Error('Ha habido algun ERROR al traducir el Texto <strong>' . $id . '</strong> al <strong>' . Lang::get($_POST['lang'])->name . '</strong><br />' . implode('<br />', $errors));
                }
            }

            // sino, mostramos la lista
            if ($action == 'list') {
                $groups = Text::groups();

                $data = Text::getAll($filters, $_SESSION['translate_lang']);

                // contamos el número de palabras (si pendiente, contamos el original)
                $nwords = 0;
                foreach ($data as $key => $reg) {
                    $nwords += Text::wcount( ($reg->pending) ? $reg->original : $reg->text );
                }
            }


            if ($action == 'edit') {
                $text = new \stdClass();

                $text->id = $id;
                $text->purpose = Text::getPurpose($id);
                $text->text = Text::getTrans($id);
            }


            return new View(
                'translate/index.html.php',
                array(
                    'section' => 'texts',
                    'action' => $action,
                    'id' => $id,
                    'text' => $text,
                    'data' => $data,
                    'nwords' => $nwords,
                    'groups' => $groups,
                    'filter' => $filter,
                    'filters' => $filters,
                    'errors' => $errors
                )
            );

        }
    }
}
