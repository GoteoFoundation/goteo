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
 *   Social Commitment
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ModelException;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Model\SocialCommitment;
use Goteo\Library\Text;
use Goteo\Model\Image;

class SocialCommitmentSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo compromiso social',
      'edit' => 'Editando compromiso social'
    );


    static protected $label = 'Compromiso social';

    public static function getId() {
        return 'social_commitment';
    }

    static public function isAllowed(\Goteo\Model\User $user, $node) {
        if( ! Config::isMasterNode($node) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id = null) {

        if(is_null($id)) $social_commitment = new SocialCommitment;
        else             $social_commitment = SocialCommitment::get($id, Config::get('lang'));

        if ($social_commitment && $this->isPost()) {
            try {
                $social_commitment->name       = $this->getPost('name');
                $social_commitment->description       = $this->getPost('description');
                $social_commitment->image = $this->getPost('image');

                if ($this->hasPost('image-' . md5($social_commitment->image) .  '-remove')) {
                    $image = Image::get($social_commitment->image);
                    $image->remove($errors);
                    $social_commitment->image = null;
                    $removed = true;
                }

                // tratar la imagen y ponerla en la propiedad image
                if(!empty($_FILES['image']['name'])) {
                    $social_commitment->image = $_FILES['image'];
                }

                if ($social_commitment->save($errors)) {
                    Message::info('Compromiso actualizado');
                    return $this->redirect();
                }
                else {
                    Message::error(implode(', ', $errors));
                }

            } catch(ModelException $e) {
                Message::error('Workshop save failed: ' . $e->getMessage());
            }
        }

        return array(
            'template' => 'admin/generic_edit',
            'data' => $social_commitment,
            'form' => [
                'action' => static::getUrl($id ? "edit/$id" : 'add'),
                'submit' => [
                    'name' => 'add',
                    'label' => Text::get('regular-save')
                ],
                'fields' => [
                    'id' => [
                        'name' => 'id',
                        'type' => 'hidden',
                    ],
                    'name' => [
                        'label' => Text::get('regular-name'),
                        'name' => 'name',
                        'type' => 'text',
                        'properties' => 'size=73'
                    ],
                    'description' => [
                        'label' => Text::get('regular-description'),
                        'name' => 'description',
                        'type' => 'textarea',
                        'properties' => 'cols=70'
                    ],
                    'image' => [
                        'label' => Text::get('regular-image'),
                        'name' => 'image',
                        'type' => 'image'
                    ]
                ]
            ]
        );
    }


    public function addAction() {
        return $this->editAction();
    }


    public function listAction() {

        $social_commitment = SocialCommitment::getAll();

        return array(
                'template' => 'admin/generic_list',
                'addbutton' => Text::get('regular-add'),
                'data' => $social_commitment,
                'url' => self::getUrl(),
                'model' => 'social_commitment',
                'columns' => [
                    'id' => 'Id',
                    'name' => Text::get('regular-name'),
                    'description' => Text::get('regular-description'),
                    'edit' => '',
                    'translate' => '',
                    'remove' => ''
                ]
        );
    }


    public function removeAction($id = null) {

        $social_commitment = SocialCommitment::get($id);

        if ($social_commitment->dbDelete()) {
            Message::info('Compromiso social eliminado correctamente');
        } else {
            Message::error('No se ha podido eliminar el compromiso social');
        }
        return $this->redirect();

    }


}
