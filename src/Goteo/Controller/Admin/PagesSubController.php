<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Application\Session,
	Goteo\Application\Lang,
	Goteo\Library\Feed,
	Goteo\Library\Page;

class PagesSubController extends AbstractSubController {

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
                if (!$this->isDefaultNode() && !in_array($id, self::$node_pages)) {
                    Message::info('No puedes gestionar la página <strong>'.$id.'</strong>');
                    return $this->redirect("/admin/pages");
                }
                // si estamos editando una página
                $page = Page::get($id, $node, Lang::getDefault('get'));

                // si llega post, vamos a guardar los cambios
                if ($this->isPost()) {
                    $page->name = $this->getPost('name');
                    $page->description = $this->getPost('description');
                    $page->content = $this->getPost('content');
                    if ($page->save($errors)) {

                        // Evento Feed
                        $log = new Feed();
                        if (!$this->isDefaultNode() && in_array($id, self::$node_pages)) {
                            $log->setTarget($node, 'node');
                        }
                        $log->populate('modificacion de página institucional (admin)', '/admin/pages',
                            \vsprintf("El admin %s ha %s la página institucional %s", array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
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
