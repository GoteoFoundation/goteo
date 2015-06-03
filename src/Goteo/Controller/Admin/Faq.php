<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Faq {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $sections = Model\Faq::sections();

            if (!isset($sections[$filters['section']])) {
                unset($filters['section']);
            }

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
                            Message::Info('Pregunta añadida correctamente');
                            break;
                        case 'edit':
                            Message::Info('Pregunta editado correctamente');
                            break;
                    }

                    // tratar si han marcado pendiente de traducir
                    if (isset($_POST['pending']) && $_POST['pending'] == 1
                        && !Model\Faq::setPending($faq->id, 'post')) {
                        Message::Error('NO se ha marcado como pendiente de traducir!');
                    }

                } else {
                    Message::Error(implode('<br />', $errors));

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => $_POST['action'],
                            'faq' => $faq,
                            'filter' => $filter,
                            'sections' => $sections
                        )
                    );
				}
			}


            switch ($action) {
                case 'up':
                    Model\Faq::up($id);
                    throw new Redirection('/admin/faq');
                    break;
                case 'down':
                    Model\Faq::down($id);
                    throw new Redirection('/admin/faq');
                    break;
                case 'add':
                    $next = Model\Faq::next($filters['section']);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'add',
                            'faq' => (object) array('section' => $filters['section'], 'order' => $next, 'cuantos' => $next),
                            'sections' => $sections
                        )
                    );
                    break;
                case 'edit':
                    $faq = Model\Faq::get($id);

                    $cuantos = Model\Faq::next($faq->section);
                    $faq->cuantos = ($cuantos -1);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'faq',
                            'file' => 'edit',
                            'action' => 'edit',
                            'faq' => $faq,
                            'sections' => $sections
                        )
                    );
                    break;
                case 'remove':
                    Model\Faq::delete($id);
                    break;
            }

            $faqs = Model\Faq::getAll($filters['section']);

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'faq',
                    'file' => 'list',
                    'faqs' => $faqs,
                    'sections' => $sections,
                    'filters' => $filters
                )
            );

        }

    }

}
