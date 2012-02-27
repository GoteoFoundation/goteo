<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
        Goteo\Library\Template;

    class Templates {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            switch ($action) {
                case 'edit':
                    // si estamos editando una plantilla
                    $template = Template::get($id);

                    // si llega post, vamos a guardar los cambios
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $template->title = $_POST['title'];
                        $template->text  = $_POST['text'];
                        if ($template->save($errors))
                            throw new Redirection("/admin/templates");
                    }


                    // sino, mostramos para editar
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'edit',
                            'template' => $template,
                            'errors'=>$errors
                        )
                     );
                    break;
                case 'list':
                    // si estamos en la lista de páginas
                    $templates = Template::getAll();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'templates',
                            'file' => 'list',
                            'templates' => $templates
                        )
                    );
                    break;
            }

        }

    }

}
