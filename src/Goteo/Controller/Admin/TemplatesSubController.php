<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */
/**
 * Gestion de plantillas de emails
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Config,
	Goteo\Library\Feed,
    Goteo\Model\Template;

class TemplatesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'templates-lb-list',
      'edit' => 'templates-lb-edit',
    );


    static protected $label = 'templates-lb';


    protected $filters = array (
      'id' => '',
      'group' => '',
      'name' => '',
    );


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node allowed here
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


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

