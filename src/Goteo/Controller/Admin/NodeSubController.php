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

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Model;

class NodeSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'node-lb-list',
      'edit' => 'node-lb-edit',
      'admins' => 'node-lb-admins',
    );


    static protected $label = 'node-lb';

    /**
     * Overwrite some permissions
     * @param  User    $user [description]
     * @param  [type]  $node [description]
     * @return boolean       [description]
     */
    static public function isAllowed(Model\User $user, $node) {
        // Central node not allowed here
        if(Config::isMasterNode($node)) return false;
        return parent::isAllowed($user, $node);
    }

    public function __construct($node, Model\User $user, Request $request) {
        parent::__construct($node, $user, $request);
        //Common data
        $this->full_node = Model\Node::get($this->node, Config::get('lang'));
        $this->contextVars([
            'node' => $this->full_node,
            'translator' => $this->isTranslator(),
            'superadmin' => $this->isSuperAdmin()
            ], '/admin/node');
    }

    public function adminsAction($id = null, $subaction = null) {
        return array(
            'template' => 'admin/node/admins'
        );
    }


    public function editAction($id = null, $subaction = null) {
        if ($this->isPost()) {
            $node = $this->full_node;
            $fields = array(
                'name',
                'subtitle',
                'email',
                'location',
                'description',
                'twitter',
                'facebook',
                'google',
                'linkedin',
                'owner_background',
                'owner_font_color',
                'owner_social_color'
            );

            foreach ($fields as $field) {
                if ($this->hasPost($field)) {
                    $node->$field = $this->getPost($field);
                }
            }

            // tratar si quitan la imagen
            if ($this->getPost('logo-' . $node->logo->hash .  '-remove')) {
                if ($node->logo instanceof Model\Image) $node->logo->remove($errors);
                $node->logo = null;
            }

            // tratar la imagen y ponerla en la propiedad logo
            if(!empty($_FILES['logo_upload']['name'])) {
                if ($node->logo instanceof Model\Image) $node->logo->remove($errors);
                $node->logo = $_FILES['logo_upload'];
            } else {
                $node->logo = (isset($node->logo->id)) ? $node->logo->id : null;
            }

            // tratar si quitan el sello
            if ($this->getPost('label-' . $node->label->hash .  '-remove')) {
                if ($node->label instanceof Model\Image) $node->label->remove($errors);
                $node->label = null;
            }

            // tratar la imagen y ponerla en la propiedad label
            if(!empty($_FILES['label_upload']['name'])) {
                if ($node->label instanceof Model\Image) $node->label->remove($errors);
                $node->label = $_FILES['label_upload'];
            } else {
                $node->label = (isset($node->label->id)) ? $node->label->id : null;
            }

             // tratar si quitan imagen de home
            if ($this->getPost('homeimg-' . $node->home_img->hash .  '-remove')) {
                if ($node->home_img instanceof Model\Image) $node->home_img->remove($errors);
                $node->home_img = null;
            }

            // tratar la imagen y ponerla en la propiedad label
            if(!empty($_FILES['homeimg_upload']['name'])) {
                if ($node->home_img instanceof Model\Image) $node->home_img->remove($errors);
                $node->home_img = $_FILES['homeimg_upload'];
            } else {
                $node->home_img = (isset($node->home_img->id)) ? $node->home_img->id : null;
            }

            /// este es el único save que se lanza desde un metodo process_
            if ($node->update($errors)) {
                Message::info('Datos del canal actualizados correctamente');
                return $this->redirect('/admin/node');
            } else {
                Message::error('Falló al actualizar los datos del canal:<br />'.implode('<br />', $errors));
            }

        }

        return array(
            'template' => 'admin/node/edit'
        );
    }


    public function listAction($id = null, $subaction = null) {
        return array(
            'template' => 'admin/node/view'
        );
    }

}


