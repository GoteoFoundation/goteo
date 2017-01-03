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
 * Apoyos institucionales
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Library\Text;
use	Goteo\Library\Feed;
use Goteo\Application\Message;
use	Goteo\Application\Config;
use Goteo\Model\Sponsor;
use Goteo\Model\Node;
use Goteo\Model\Image;

class SponsorsSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'sponsors-lb-list',
      'add' => 'sponsors-lb-add',
      'edit' => 'sponsors-lb-edit',
      );


    static protected $label = 'sponsors-lb';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        try{
            $nodeData = Node::get($node);
        } catch(\Exception $e){}
        $limit = (int) $nodeData->sponsors_limit;

        // Only central node allowed here and nodes where sponsors_limit>0

        if( !Config::isMasterNode($node) && !$limit ) return false;
        return parent::isAllowed($user, $node);
    }

    private function checkItemPermission($id = null) {
        if($sponsor = Sponsor::get($id)) {
            if($sponsor->node === $this->node) return true;
            throw new ControllerAccessDeniedException('You cannot admin this sponsor');
        }
        elseif(!$this->isMasterNode()) {
            // check max number of sponsors
            $node = Node::get($this->node);
            $limit = (int) $node->sponsors_limit;
            $count = Sponsor::getList($this->node, 0 , 0, true);
            if($count >= $limit) {
                throw new ControllerAccessDeniedException('Max number of sponsor reached!');
            }
        }
    }

    public function editAction($id = null, $subaction = null) {
        $this->checkItemPermission($id);
        // gestionar post
        if ($this->isPost()) {
            $id = $this->getPost('id');
            // instancia
            $item = new Sponsor(array(
                'id' => $id,
                'name' => $this->getPost('name'),
                'node' => $this->getPost('node'),
                'image' => $this->getPost('image'),
                'url' => $this->getPost('url'),
                'order' => $this->getPost('order')
            ));
            // tratar si quitan la imagen
            if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                $image = Image::get($item->image);
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
                return $this->redirect(static::getUrl());
            } else {
                Message::error('No se han podido grabar los datos. ' . implode(', ', $errors));
            }
        } else {
            $item = Sponsor::get($id);
        }

        return array(
            'template' => 'admin/generic_edit',
            'data' => $item,
            'translator' => $this->isTranslator(),
            'form' => array(
                'action' => static::getUrl('edit', $id),
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
    }


    /**
    * Just the form
    */
    public function addAction() {
        $this->checkItemPermission();
        return array(
            'template' => 'admin/generic_edit',
            'data' => (object) array('order' => Sponsor::next($this->node), 'node' => $this->node ),
            'form' => array(
                'action' => static::getUrl('edit'),
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
    }


    public function listAction() {
        $data = Sponsor::getAll($this->node);
        return array(
            'template' => 'admin/generic_list',
            'addbutton' => 'Nuevo patrocinador',
            'data' => $data,
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
            'url' => static::getUrl()
        );

    }

    public function upAction($id = null, $subaction = null) {
        $this->checkItemPermission($id);
        Sponsor::up($id, $this->node);
        return $this->redirect(static::getUrl());
    }

    public function downAction($id = null, $subaction = null) {
        $this->checkItemPermission($id);
        Sponsor::down($id, $this->node);
        return $this->redirect(static::getUrl());
    }

    public function removeAction($id = null, $subaction = null) {
        $this->checkItemPermission($id);

        if (Sponsor::delete($id)) {
            Message::info('Se ha eliminado el registro');
        } else {
            Message::info('No se ha podido eliminar el registro');
        }
        return $this->redirect(static::getUrl());
    }

}
