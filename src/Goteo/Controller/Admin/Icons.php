<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Application\Message,
		Goteo\Application\Session,
		Goteo\Library\Feed,
        Goteo\Model;

    class Icons {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $groups = Model\Icon::groups();

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $icon = new Model\Icon(array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'group' => empty($_POST['group']) ? null : $_POST['group']
                ));

				if ($icon->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            Message::info('Nuevo tipo añadido correctamente');
                            break;
                        case 'edit':
                            Message::info('Tipo editado correctamente');

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('modificacion de tipo de retorno/recompensa (admin)', '/admin/icons',
                                \vsprintf("El admin %s ha %s el tipo de retorno/recompensa %s", array(
                                    Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                    Feed::item('relevant', 'Modificado'),
                                    Feed::item('project', $icon->name)
                            )));
                            $log->doAdmin('admin');
                            unset($log);

                            break;
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Icon::setPending($icon->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                }
				else {
                    Message::error(implode('<br />', $errors));

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'icons',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'icon' => $icon,
                            'groups' => $groups
                        )
                    );
				}
			}

            switch ($action) {
                case 'edit':
                    $icon = Model\Icon::get($id);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'icons',
                            'file' => 'edit',
                            'action' => 'edit',
                            'icon' => $icon,
                            'groups' => $groups
                        )
                    );
                    break;
            }

            $icons = Model\Icon::getAll($filters['group']);
            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'icons',
                    'file' => 'list',
                    'icons' => $icons,
                    'groups' => $groups,
                    'filters' => $filters
                )
            );

        }

    }

}
