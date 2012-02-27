<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Model;

    class Faq {

        public static function process ($action = 'list', $id = null, $filter = 'node') {

            $sections = Model\Faq::sections();
            
            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // instancia
                $faq = new Model\Faq(array(
                    'id' => $_POST['id'],
                    'node' => \GOTEO_NODE,
                    'section' => $_POST['section'],
                    'title' => $_POST['title'],
                    'description' => $_POST['description'],
                    'order' => $_POST['order'],
                    'move' => $_POST['move']
                ));

				if ($faq->save($errors)) {
                    switch ($_POST['action']) {
                        case 'add':
                            $success = 'Pregunta añadida correctamente';
                            break;
                        case 'edit':
                            $success = 'Pregunta editado correctamente';
                            break;
                    }
				}
				else {
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'faq' => $faq,
                            'filter' => $filter,
                            'sections' => $sections,
                            'errors' => $errors
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Faq::up($id);
                    break;
                case 'down':
                    Model\Faq::down($id);
                    break;
                case 'add':
                    $next = Model\Faq::next($filter);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'add',
                            'faq' => (object) array('section' => $filter, 'order' => $next, 'cuantos' => $next),
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'edit':
                    $faq = Model\Faq::get($id);

                    $cuantos = Model\Faq::next($faq->section);
                    $faq->cuantos = ($cuantos -1);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'edit',
                            'faq' => $faq,
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'remove':
                    Model\Faq::delete($id);
                    break;
            }

            $faqs = Model\Faq::getAll($filter);

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'faq',
                    'file' => 'list',
                    'faqs' => $faqs,
                    'sections' => $sections,
                    'filter' => $filter,
                    'errors' => $errors,
                    'success' => $success
                )
            );
            
        }

    }

}
