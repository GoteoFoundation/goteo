<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Application\Message,
    Goteo\Model;

class SponsorsSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null) {

        $node = $this->node;

        $model = 'Goteo\Model\Sponsor';
        $url = '/admin/sponsors';

        $errors = array();

        switch ($action) {
            case 'add':
                return array(
                        'folder' => 'base',
                        'file' => 'edit',
                        'data' => (object) array('order' => $model::next($node), 'node' => $node ),
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
                                'node' => array(
                                    'label' => '',
                                    'name' => 'node',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Patrocinador',
                                    'name' => 'name',
                                    'type' => 'text'
                                ),
                                'url' => array(
                                    'label' => 'Enlace',
                                    'name' => 'url',
                                    'type' => 'text',
                                    'properties' => 'size=100'
                                ),
                                'image' => array(
                                    'label' => 'Logo',
                                    'name' => 'image',
                                    'type' => 'image'
                                ),
                                'order' => array(
                                    'label' => 'Posición',
                                    'name' => 'order',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'edit':

                // gestionar post
                if ($this->isPost()) {

                    // instancia
                    $item = new $model(array(
                        'id' => $this->getPost('id'),
                        'name' => $this->getPost('name'),
                        'node' => $this->getPost('node'),
                        'image' => $this->getPost('image'),
                        'url' => $this->getPost('url'),
                        'order' => $this->getPost('order')
                    ));

                    // tratar si quitan la imagen
                    if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                        $image = Model\Image::get($item->image);
                        $image->remove($errors);
                        $item->image = null;
                        $removed = true;
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image']['name'])) {
                        $item->image = $_FILES['image'];
                    }

                    if ($item->save($errors)) {
                        Message::info('Datos grabados correctamente');
                        return $this->redirect($url);
                    } else {
                        Message::error('No se han podido grabar los datos. ' . implode(', ', $errors));
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
                                'node' => array(
                                    'label' => '',
                                    'name' => 'node',
                                    'type' => 'hidden'

                                ),
                                'name' => array(
                                    'label' => 'Patrocinador',
                                    'name' => 'name',
                                    'type' => 'text'
                                ),
                                'url' => array(
                                    'label' => 'Enlace',
                                    'name' => 'url',
                                    'type' => 'text',
                                    'properties' => 'size=100'
                                ),
                                'image' => array(
                                    'label' => 'Logo',
                                    'name' => 'image',
                                    'type' => 'image'
                                ),
                                'order' => array(
                                    'label' => 'Posición',
                                    'name' => 'order',
                                    'type' => 'text'
                                )
                            )
                    )
                );

                break;
            case 'up':
                $model::up($id, $node);
                return $this->redirect($url);
                break;
            case 'down':
                $model::down($id, $node);
                return $this->redirect($url);
                break;
            case 'remove':
                if ($model::delete($id)) {
                    Message::info('Se ha eliminado el registro');
                    return $this->redirect($url);
                } else {
                    Message::info('No se ha podido eliminar el registro');
                }
                break;
        }

        return array(
                'folder' => 'base',
                'file' => 'list',
                'addbutton' => 'Nuevo patrocinador',
                'data' => $model::getAll($node),
                'columns' => array(
                    'edit' => '',
                    'name' => 'Patrocinador',
                    'url' => 'Enlace',
                    'image' => 'Imagen',
                    'order' => 'Posición',
                    'up' => '',
                    'down' => '',
                    'remove' => ''
                ),
                'url' => "$url"
        );

    }

}
