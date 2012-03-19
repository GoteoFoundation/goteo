<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Model;

    class Licenses {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            // agrupaciones de mas a menos abertas
            $groups = Model\License::groups();

            // tipos de retorno para asociar
            $icons = Model\Icon::getAll('social');


            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // objeto
                $license = new Model\License(array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'description' => $_POST['description'],
                    'url' => $_POST['url'],
                    'group' => $_POST['group'],
                    'order' => $_POST['order'],
                    'icons' => $_POST['icons']
                ));

				if ($license->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success[] = 'Licencia añadida correctamente';
                            break;
                        case 'edit':
                            $success[] = 'Licencia editada correctamente';

                            // Evento Feed
                            $log = new Feed();
                            $log->populate('modificacion de licencia (admin)', '/admin/licenses',
                                \vsprintf("El admin %s ha %s la licencia %s", array(
                                    Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                    Feed::item('relevant', 'Modificado'),
                                    Feed::item('project', $license->name)
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
                            'folder' => 'licenses',
                            'file' => 'edit',
                            'action'  => $_POST['action'],
                            'license' => $license,
                            'icons'   => $icons,
                            'groups'  => $groups,
                            'errors'  => $errors
                        )
                    );
				}
			}

            switch ($action) {
                case 'up':
                    Model\License::up($id);
                    break;
                case 'down':
                    Model\License::down($id);
                    break;
                case 'add':
                    $next = Model\License::next();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'licenses',
                            'file' => 'edit',
                            'action' => 'add',
                            'license' => (object) array('order' => $next, 'icons' => array()),
                            'icons' => $icons,
                            'groups' => $groups
                        )
                    );
                    break;
                case 'edit':
                    $license = Model\License::get($id);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'licenses',
                            'file' => 'edit',
                            'action' => 'edit',
                            'license' => $license,
                            'icons' => $icons,
                            'groups' => $groups
                        )
                    );
                    break;
                case 'remove':
    //                Model\License::delete($id);
                    break;
            }

            $licenses = Model\License::getAll($filters['icon'], $filters['group']);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'licenses',
                    'file' => 'list',
                    'licenses' => $licenses,
                    'filters'  => $filters,
                    'groups' => $groups,
                    'icons'    => $icons,
                    'errors' => $errors,
                    'success' => $success
                )
            );
            
        }

    }

}
