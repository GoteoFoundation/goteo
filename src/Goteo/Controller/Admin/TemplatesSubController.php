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
use Goteo\Application\Config;
use Goteo\Library\Feed;
use Goteo\Model\Template;

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


    public function editAction($id = null, $subaction = null, $filters=array()) {
        // si estamos editando una plantilla
        $template = Template::get($id, Config::get('lang'));

        // si llega post, vamos a guardar los cambios
        if ($this->isPost()) {
            $template->title = $this->getPost('title');
            $template->text  = $this->getPost('text');
            $template->type  = $this->getPost('type');
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
            'template' => 'admin/templates/edit',
            'edit' => $template
         );
    }


    public function listAction($id = null, $subaction = null, $filters=array()) {

        // si estamos en la lista de páginas
        $templates = Template::getAll($filters);
        $groups= Template::groups();


        return array(
            'template' => 'admin/templates/list',
            'templates' => $templates,
            'groups' => $groups,
            'filters' => $filters
        );
    }

}

