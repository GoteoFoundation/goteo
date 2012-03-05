<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
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
                            $success[] = 'Nuevo tipo añadido correctamente';
                            break;
                        case 'edit':
                            $success[] = 'Tipo editado correctamente';

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('modificacion de tipo de retorno/recompensa (admin)', '/admin/icons',
                                \vsprintf("El admin %s ha %s el tipo de retorno/recompensa %s", array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', 'Modificado'),
                                    Feed::item('project', $icon->name)
                            )));
                            $log->doAdmin('admin');
                            unset($log);

                            break;
                    }
				}
				else {
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'icons',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'icon' => $icon,
                            'groups' => $groups,
                            'errors' => $errors
                        )
                    );
				}
			}

            switch ($action) {
                case 'edit':
                    $icon = Model\Icon::get($id);

                    return new View(
                        'view/admin/index.html.php',
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
                'view/admin/index.html.php',
                array(
                    'folder' => 'icons',
                    'file' => 'list',
                    'icons' => $icons,
                    'groups' => $groups,
                    'filters' => $filters,
                    'errors' => $errors,
                    'success' => $success
                )
            );
            
        }

    }

}
