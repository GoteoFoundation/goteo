<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class TagsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $model = 'Goteo\Model\Blog\Post\Tag';
        $url = '/admin/tags';

        $errors = array();

        switch ($action) {
            case 'add':
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => (object) array(),
                        'form' => array(
                            'action' => "$url/edit/",
                            'submit' => array(
                                'name' => 'update',
                                'label' => 'Añadir'
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Tag',
                                    'name' => 'name',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost() && $this->hasPost('update')) {

                    $errors = array();

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name')
                    ));

                    if ($item->save($errors)) {

                        // tratar si han marcado pendiente de traducir
                        if ($this->getPost('pending') == 1 && !Model\Blog\Post\Tag::setPending($item->id, 'post')) {
                            Message::error('NO se ha marcado como pendiente de traducir!');
                        }

                        return $this->redirect($url);
                    } else {
                        Message::error(implode('<br />', $errors));
                    }
                } else {
                    $item = $model::get($id);
                }

                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => $item,
                        'form' => array(
                            'action' => "$url/edit/$id",
                            'submit' => array(
                                'name' => 'update',
                                'label' => Text::get('regular-save')
                            ),
                            'fields' => array (
                                'id' => array(
                                    'label' => '',
                                    'name' => 'id',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Tag',
                                    'name' => 'name',
                                    'type' => 'text'
                                )
                            )
                    )
                );

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
                'model' => 'tag',
                'addbutton' => 'Nuevo tag',
                'data' => $model::getList(1),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Tag',
                    'used' => 'Entradas',
                    'translate' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}

