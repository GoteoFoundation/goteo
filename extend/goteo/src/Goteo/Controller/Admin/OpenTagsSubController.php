<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
    Goteo\Model;

class OpenTagsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $model = 'Goteo\Model\OpenTag';
        $url = '/admin/open_tags';

        $errors = array();

        if ($this->isPost()) {

                if( ! ($post = $this->getPost('item')) ) {
                    error_log($el_item);
                    $post = null;
                }

                // objeto
                $open_tag = new Model\OpenTag(array(
                    'id' => $this->getPost('id'),
                    'name' => $this->getPost('name'),
                    'description' => $this->getPost('description'),
                    'order' => $this->getPost('order'),
                    'post' => $post
                ));

                if ($open_tag->save($errors)) {
                    Message::info('Datos guardados');

                    // tratar si han marcado pendiente de traducir
                    if ($this->getPost('pending') == 1 && !Model\OpenTag::setPending($open_tag->id, 'post')) {
                        Message::error('NO se ha marcado como pendiente de traducir!');
                    }

                    return $this->redirect('/admin/open_tags');
                }

                else {
                Message::error(implode('<br />', $errors));

                // otros elementos disponibles
                $items = Model\Post::getAutocomplete();

                switch ($this->getPost('action')) {
                    case 'add':
                        return array(
                                'folder' => 'open_tags',
                                'file' => 'edit',
                                'action' => 'add',
                                'open_tag' => $open_tag,
                                'items' => $items,
                                'autocomplete' => true
                        );
                        break;
                    case 'edit':
                        return array(
                                'folder' => 'open_tags',
                                'file' => 'edit',
                                'action' => 'edit',
                                'story' => $open_tag,
                                'items' => $items,
                                'autocomplete' => true
                        );
                        break;
                }
            }
        }




        switch ($action) {

            case 'edit':

                $open_tag = Model\OpenTag::get($id);
                    // elementos disponibles
                    $items = Model\Post::getAutocomplete();

                    return array(
                            'folder' => 'open_tags',
                            'file' => 'edit',
                            'action' => 'edit',
                            'open_tag' => $open_tag,
                            'items' => $items,
                            'autocomplete' => true
                    );

                break;

            case 'add':
                // siguiente orden
                $next = Model\OpenTag::next();
                // elementos disponibles
                $items = Model\Post::getAutocomplete();

                return array(
                        'folder' => 'open_tags',
                        'file' => 'edit',
                        'action' => 'add',
                        'open_tag' => (object) array('order' => $next),
                        'items' => $items,
                        'autocomplete' => true
                );
                break;

            case 'up':
                $model::up($id);
                break;
            case 'down':
                $model::down($id);
                break;
            case 'remove':
                if ($model::delete($id)) {
                    return $this->redirect($url);
                }
                break;
        }

        return array(
                'folder' => 'base',
                'file' => 'list',
                'model' => 'open_tag',
                'addbutton' => 'Nueva agrupación',
                'data' => $model::getAll(),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Agrupación',
                    'numProj' => 'Proyectos',
                    'order' => 'Prioridad',
                    'translate' => '',
                    'up' => '',
                    'down' => '',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}
