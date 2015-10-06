<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Application\Config,
    Goteo\Application\Session,
	Goteo\Application\Lang,
	Goteo\Library\Feed,
    Goteo\Library\Page;

class PagesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nueva Página',
      'edit' => 'Editando Página',
      'translate' => 'Traduciendo Página',
    );


    static protected $label = 'Páginas';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function translateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('translate', $id, $this->getFilters(), $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->getFilters(), $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->getFilters(), $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    static public $node_pages = array('about', 'contact', 'press', 'service');


    public function process ($action = 'list', $id = null) {

        $node = $this->node;

        $errors = array();

        switch ($action) {
            case 'add':
                if ($this->isPost()) {
                    $page = new Page();
                    $page->id = $this->getPost('id');
                    $page->name = $this->getPost('name');
                    if ($page->add($errors)) {

                        Message::info('La página <strong>'.$page->name. '</strong> se ha creado correctamente, se puede editar ahora.');

                        return $this->redirect("/admin/pages/edit/{$page->id}");
                    } else {
                        Message::error('No se ha creado bien '. implode('<br />', $errors));
                        return $this->redirect("/admin/pages/add");
                    }
                }

                return array(
                        'folder' => 'pages',
                        'file' => 'add'
                 );
                break;

            case 'edit':
                if (!$this->isMasterNode() && !in_array($id, self::$node_pages)) {
                    Message::info('No puedes gestionar la página <strong>'.$id.'</strong>');
                    return $this->redirect("/admin/pages");
                }
                // si estamos editando una página
                $page = Page::get($id, $node);

                // si llega post, vamos a guardar los cambios
                if ($this->isPost()) {
                    $page->name = $this->getPost('name');
                    $page->description = $this->getPost('description');
                    $page->content = $this->getPost('content');
                    if ($page->save($errors)) {

                        // Evento Feed
                        $log = new Feed();
                        if (!$this->isMasterNode() && in_array($id, self::$node_pages)) {
                            $log->setTarget($node, 'node');
                        }
                        $log->populate('modificacion de página institucional (admin)', '/admin/pages',
                            \vsprintf("El admin %s ha %s la página institucional %s", array(
                            Feed::item('user', $this->user->name, $this->user->id),
                            Feed::item('relevant', 'Modificado'),
                            Feed::item('relevant', $page->name, $page->url)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                        Message::info('La página '.$page->name. ' se ha actualizado correctamente');

                        // tratar si han marcado pendiente de traducir
                        // no usamos Core\Model porque no es tabla _lang
                        if ($this->getPost('pending') == 1) {
                            $ok = Page::setPending($id, $node, $errors);
                            if (!$ok) {
                                Message::error(implode('<br />', $errors));
                            }
                        }

                        return $this->redirect("/admin/pages");
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                }


                // sino, mostramos para editar
                return array(
                        'folder' => 'pages',
                        'file' => 'edit',
                        'page' => $page
                 );
                break;

            case 'list':
                // si estamos en la lista de páginas
                $pages = Page::getList($node);

                return array(
                        'folder' => 'pages',
                        'file' => 'list',
                        'pages' => $pages,
                        'node' => $node
                );
                break;
        }

    }

}
