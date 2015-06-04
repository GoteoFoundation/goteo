<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Criteria {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $sections = Model\Criteria::sections();

            if (!isset($sections[$filters['section']])) {
                unset($filters['section']);
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $criteria = new Model\Criteria(array(
                    'id' => $_POST['id'],
                    'section' => $_POST['section'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'move' => $_POST['move']
                ));

				if ($criteria->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            Message::info('Criterio añadido correctamente');
                            break;
                        case 'edit':
                            Message::info('Criterio editado correctamente');
                            break;
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Criteria::setPending($criteria->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                } else {
                    Message::error(implode('<br />', $errors));

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'criteria',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'criteria' => $criteria,
                            'sections' => $sections
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Criteria::up($id);
                    break;
                case 'down':
                    Model\Criteria::down($id);
                    break;
                case 'add':
                    $next = Model\Criteria::next($filters['section']);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'criteria',
                            'file' => 'edit',
                            'action' => 'add',
                            'criteria' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                            'sections' => $sections
                        )
                    );
                    break;
                case 'edit':
                    $criteria = Model\Criteria::get($id);

                    $cuantos = Model\Criteria::next($criteria->section);
                    $criteria->cuantos = ($cuantos -1);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'criteria',
                            'file' => 'edit',
                            'action' => 'edit',
                            'criteria' => $criteria,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'remove':
                    Model\Criteria::delete($id);
                    break;
            }

            $criterias = Model\Criteria::getAll($filters['section']);

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'criteria',
                    'file' => 'list',
                    'criterias' => $criterias,
                    'sections' => $sections,
                    'filters' => $filters
                )
            );

        }

    }

}
