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

use Exception;
use Goteo\Application\Config;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Message;
use Goteo\Library\Text;
use Goteo\Model\Image;
use Goteo\Model\Node;
use Goteo\Model\Sponsor;
use Goteo\Model\User;

class SponsorsSubController extends AbstractSubController {

    static protected $labels = [
        'list' => 'sponsors-lb-list',
        'add' => 'sponsors-lb-add',
        'edit' => 'sponsors-lb-edit',
    ];

    static protected $label = 'sponsors-lb';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(User $user, $node): bool {
        try{
            $nodeData = Node::get($node);
        } catch(Exception $e){ }

        $limit = (int) $nodeData->sponsors_limit;

        // Only central node allowed here and nodes where sponsors_limit > 0
        if( !Config::isMasterNode($node) && !$limit ) return false;

        return parent::isAllowed($user, $node);
    }

    private function checkItemPermission($id = null) {
        if($sponsor = Sponsor::get(['node' => $id])) {
            if($sponsor->node === $this->node) return true;
            throw new ControllerAccessDeniedException('You cannot admin this sponsor');
        }
        elseif(!$this->isMasterNode()) {
            // check max number of sponsors
            $node = Node::get($this->node);
            $limit = (int) $node->sponsors_limit;
            $count = Sponsor::getList(['node' => $this->node], 0 , 0, true);
            if($count >= $limit) {
                throw new ControllerAccessDeniedException('Max number of sponsor reached!');
            }
        }
    }

    public function editAction($id = null) {
        $this->checkItemPermission($id);

        if ($this->isPost()) {
            $id = $this->getPost('id');
            $item = new Sponsor([
                'id' => $id,
                'name' => $this->getPost('name'),
                'node' => $this->getPost('node'),
                'image' => $this->getPost('image'),
                'url' => $this->getPost('url'),
                'order' => $this->getPost('order')
            ]);

            if ($this->hasPost('image-' . md5($item->image) .  '-remove')) {
                $image = Image::get($item->image);
                $image->remove($errors);
                $item->image = null;
            }

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

        return [
            'template' => 'admin/generic_edit',
            'data' => $item,
            'translator' => $this->isTranslator(),
            'form' => $this->getFormFields(Text::get('regular-save'))
        ];
    }

    private function getFormFields(string $submitBtnText, $id = null): array
    {
        return [
            'action' => static::getUrl('edit', $id),
            'submit' => [
                'name' => 'update',
                'label' => $submitBtnText
            ],
            'fields' => [
                'id' => [
                    'label' => '',
                    'name' => 'id',
                    'type' => 'hidden'
                ],
                'node' => [
                    'label' => '',
                    'name' => 'node',
                    'type' => 'hidden'
                ],
                'name' => [
                    'label' => 'Patrocinador',
                    'name' => 'name',
                    'type' => 'text'
                ],
                'url' => [
                    'label' => 'Enlace',
                    'name' => 'url',
                    'type' => 'text',
                    'properties' => 'size=100'
                ],
                'image' => [
                    'label' => 'Logo',
                    'name' => 'image',
                    'type' => 'image'
                ],
                'order' => [
                    'label' => 'Posición',
                    'name' => 'order',
                    'type' => 'text'
                ]
            ]
        ];
    }

    /**
    * Just the form
    */
    public function addAction() {
        $this->checkItemPermission();

        return [
            'template' => 'admin/generic_edit',
            'data' => (object) ['order' => Sponsor::next($this->node), 'node' => $this->node],
            'form' => $this->getFormFields('Añadir')
        ];
    }

    public function listAction() {
        $data = Sponsor::getAll($this->node);
        return [
            'template' => 'admin/generic_list',
            'addbutton' => 'Nuevo patrocinador',
            'data' => $data,
            'columns' => [
                'edit' => '',
                'name' => 'Patrocinador',
                'url' => 'Enlace',
                'image' => 'Imagen',
                'order' => 'Posición',
                'up' => '',
                'down' => '',
                'remove' => ''
            ],
            'url' => static::getUrl()
        ];
    }

    public function upAction($id = null) {
        $this->checkItemPermission($id);
        Sponsor::up($id, $this->node);
        return $this->redirect(static::getUrl());
    }

    public function downAction($id = null) {
        $this->checkItemPermission($id);
        Sponsor::down($id, $this->node);
        return $this->redirect(static::getUrl());
    }

    public function removeAction($id = null) {
        $this->checkItemPermission($id);

        if (Sponsor::delete($id)) {
            Message::info('Se ha eliminado el registro');
        } else {
            Message::info('No se ha podido eliminar el registro');
        }

        return $this->redirect(static::getUrl());
    }

}
