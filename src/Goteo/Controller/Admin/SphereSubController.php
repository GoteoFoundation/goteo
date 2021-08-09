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
 *   sphere
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Exception;
use Goteo\Application\Message;
use Goteo\Model;
use Goteo\Model\Sphere;
use Goteo\Model\User;


class SphereSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'add' => 'Nuevo ámbito',
      'edit' => 'Editando ámbito'
    );


    static protected $label = 'Ámbitos de convocatoria';

     /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(User $user, $node): bool {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id) {

        $sphere = Model\Sphere::get($id, Config::get('sql_lang'));

        if ($sphere && $this->isPost()) {

            try {
                $sphere->name       = $this->getPost('name');
                $sphere->landing_match       = $this->getPost('landing_match');
                $sphere->order       = $this->getPost('order');

                // Image
                if ($this->getPost('image-' . $sphere->image->hash .  '-remove')) {
                    if ($sphere->image instanceof Model\Image) $sphere->image->remove($errors);
                    $sphere->image = null;
                }

                // New image
                if(!empty($_FILES['image']['name'])) {
                    if ($sphere->image instanceof Model\Image) $sphere->image->remove($errors);
                    $sphere->image = $_FILES['image'];
                } else {
                    $sphere->image = $sphere->image->id;
                }

                if ($sphere->save($errors)) {

                    return $this->redirect();
                }
                else {

                    Message::error(implode(', ', $errors));
                }
            } catch(Exception\ModelException $e) {
                Message::error('sphere not found: ' . $e->getMessage());
            }
        }

        return array(
                'template' => 'admin/sphere/edit',
                'action' => '/admin/sphere/edit/' . $sphere->id,
                'sphere' => $sphere,
        );
    }


    public function addAction() {

        $sphere = new Sphere;

        if ($sphere && $this->isPost()) {

            try {
                $sphere->name = $this->getPost('name');
                $sphere->landing_match       = $this->getPost('landing_match');
                $sphere->order       = $this->getPost('order');

                // New image
                if(!empty($_FILES['image']['name'])) {
                    if ($sphere->image instanceof Model\Image) $sphere->image->remove($errors);
                    $sphere->image = $_FILES['image'];
                } else {
                    $sphere->image = $sphere->image->id;
                }


                if ($sphere->save($errors)) {

                    return $this->redirect();
                }
                else {

                    Message::error(implode(', ', $errors));
                }
            } catch(Exception\ModelException $e) {
                Message::error('Sphere not found: ' . $e->getMessage());
            }
        }

        return array(
                'template' => 'admin/sphere/edit',
                'action' => '/admin/sphere/add'
        );
    }


    public function listAction() {

        $spheres = Model\Sphere::getAll();

        return array(
                'template' => 'admin/sphere/list',
                'spheres' => $spheres
        );
    }


    public function removeAction($id = null) {

        $sphere = Model\Sphere::get($id);

        if ($sphere->dbDelete()) {
            Message::info('Ámbito eliminado correctamente');
        } else {
            Message::error('No se ha podido quitar el ámbito');
        }
        return $this->redirect();

    }



}
