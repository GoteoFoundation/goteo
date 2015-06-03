<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Library\Template;

    class Templates {

        public static function process ($action = 'list', $id = null, $filters = array()) {

            $errors = array();

            // valores de filtro
            $groups    = Template::groups();

            switch ($action) {
                case 'edit':
                    // si estamos editando una plantilla
                    $template = Template::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $template->title = $_POST['title'];
                        $template->text  = $_POST['text'];
                        if ($template->save($errors)) {
                            Message::Info('La plantilla se ha actualizado correctamente');

                            // tratar si han marcado pendiente de traducir
                            if (isset($_POST['pending']) && $_POST['pending'] == 1
                                && !\Goteo\Core\Model::setPending($id, 'template')) {
                                Message::Error('NO se ha marcado como pendiente de traducir!');
                            }

                            throw new Redirection("/admin/templates");
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }
                    }


                    // sino, mostramos para editar
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'edit',
                            'template' => $template
                        )
                     );
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $templates = Template::getAll($filters);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'list',
                            'templates' => $templates,
                            'groups' => $groups,
                            'filters' => $filters
                        )
                    );
                    break;
                default:
                    Message::Error('No se ha especificado una acción válida para plantillas en la URL');
                    throw new Redirection('/admin/templates');
            }

        }

    }

}
