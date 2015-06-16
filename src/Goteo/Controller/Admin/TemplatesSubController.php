<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Library\Template;

class TemplatesSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array()) {

        $errors = array();

        // valores de filtro
        $groups    = Template::groups();

        switch ($action) {
            case 'edit':
                // si estamos editando una plantilla
                $template = Template::get($id);

                // si llega post, vamos a guardar los cambios
                if ($this->isPost()) {
                    $template->title = $this->getPost('title');
                    $template->text  = $this->getPost('text');
                    if ($template->save($errors)) {
                        Message::info('La plantilla se ha actualizado correctamente');

                        // tratar si han marcado pendiente de traducir
                        if ($this->getPost('pending') == 1 && !\Goteo\Core\Model::setPending($id, 'template')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        return $this->redirect("/admin/templates");
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                }


                // sino, mostramos para editar
                return array(
                        'folder' => 'templates',
                        'file' => 'edit',
                        'template' => $template
                 );
                break;
            case 'list':
                // si estamos en la lista de páginas
                $templates = Template::getAll($filters);

                return array(
                        'folder' => 'templates',
                        'file' => 'list',
                        'templates' => $templates,
                        'groups' => $groups,
                        'filters' => $filters
                );
                break;
            default:
                Message::error('No se ha especificado una acción válida para plantillas en la URL');
                return $this->redirect('/admin/templates');
        }

    }

}

