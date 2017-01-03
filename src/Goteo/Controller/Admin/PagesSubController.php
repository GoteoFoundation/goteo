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

use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Application\Lang;
use Goteo\Library\Feed;
use Goteo\Model\Page;

class PagesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'pages-lb-list',
      'add' => 'pages-lb-add',
      'edit' => 'pages-lb-edit',
      'translate' => 'pages-lb-translate',
    );


    static protected $label = 'pages-lb';


    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function addAction($id = null, $subaction = null) {
        $errors = array();
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
                'template' => 'admin/pages/add'
         );
    }


    public function editAction($id = null, $subaction = null) {
        $errors = array();

        if (!$this->isMasterNode()) {
            Message::info('No puedes gestionar la página <strong>'.$id.'</strong>');
            return $this->redirect("/admin/pages");
        }
        // si estamos editando una página
        $page = Page::get($id, Lang::getDefault());

        // si llega post, vamos a guardar los cambios
        if ($this->isPost()) {
            $page->name = $this->getPost('name');
            $page->description = $this->getPost('description');
            $page->content = $this->getPost('content');
            $page->type = $this->getPost('type');
            if ($page->save($errors)) {

                // Evento Feed
                $log = new Feed();
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
                if ($this->hasPost('pending') && $this->getPost('pending') == 1
                    && !Page::setPending($post->id, 'page')) {
                    Message::error('NO se ha marcado como pendiente de traducir!');
                }

                return $this->redirect("/admin/pages");
            } else {
                Message::error(implode('<br />', $errors));
            }
        }


        // sino, mostramos para editar
        return array(
                'template' => 'admin/pages/edit',
                'page' => $page
         );
    }


    public function listAction($id = null, $subaction = null) {
        // si estamos en la lista de páginas
        $pages = Page::getList();

        return array(
                'template' => 'admin/pages/list',
                'pages' => $pages
        );
    }



}
