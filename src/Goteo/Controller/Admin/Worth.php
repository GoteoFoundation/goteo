<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
		Goteo\Library\Feed,
        Goteo\Library\Worth as WorthLib;

    class Worth {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'edit') {

                // instancia
                $data = array(
                    'id' => $_POST['id'],
                    'name' => $_POST['name'],
                    'amount' => $_POST['amount']
                );

				if (WorthLib::save($data, $errors)) {
                    $action = 'list';
                    Message::Info('Nivel de meritocracia modificado');

                    // Evento Feed
                    $log = new Feed();
                    $log->populate('modificacion de meritocracia (admin)', '/admin/worth',
                        \vsprintf("El admin %s ha %s el nivel de meritocrácia %s", array(
                            Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                            Feed::item('relevant', 'Modificado'),
                            Feed::item('project', $data->name)
                    )));
                    $log->doAdmin('admin');
                    unset($log);

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !\Goteo\Core\Model::setPending($data->id, 'worth')) {
                        Message::Error('NO se ha marcado como pendiente de traducir!');
                    }

                }
				else {
                    Message::Error(implode('<br />', $errors));

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'worth',
                            'file' => 'edit',
                            'action' => 'edit',
                            'worth' => (object) $data
                        )
                    );
				}
			}

            switch ($action) {
                case 'edit':
                    $worth = WorthLib::getAdmin($id);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'worth',
                            'file' => 'edit',
                            'action' => 'edit',
                            'worth' => $worth
                        )
                    );
                    break;
            }

            $worthcracy = WorthLib::getAll();

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'worth',
                    'file' => 'list',
                    'worthcracy' => $worthcracy
                )
            );

        }

    }

}
