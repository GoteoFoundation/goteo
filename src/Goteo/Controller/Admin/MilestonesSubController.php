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
 * Milestones manage
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception;
use Goteo\Library\Feed;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Model;
use Goteo\Model\Milestone;
use Goteo\Application\Config;



class MilestonesSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'milestones-lb-list',
      'add' => 'milestones-lb-add',
      'edit' => 'milestones-lb-edit',
      'translate' => 'milestones-lb-translate',
      'keywords' => 'milestones-lb-keywords',
    );


    static protected $label = 'milestones-lb';

    public static $types = array (
      'on-publish' => 'Publicación',
      'day-3' => 'Día 3',
      'day-5' => 'Día 5',
      'day-7' => 'Día 7',
      'day-20' => 'Día 20',
      'day-33' => 'Día 33',
      'day-37' => 'Día 37',
      'day-40' => 'Día 40',
      'day-40-fail' => 'Día 40 fallido',
      'day-75' => 'Día 75',
      'day-80' => 'Día 80',
      'rewards-fullfilled' => 'Recompensas envíadas',
      'social-rewards-fullfilled' => 'Envíados retornos colectivos',
      '20-percent-reached' => '20% recaudación',
      '50-percent-reached' => '50% recaudación',
      '90-percent-reached' => '90% recaudación',
      '100-percent-reached' => '100% recaudación',
      '200-percent-reached' => '200% recaudación',
      'invest-500' => 'Donacion 500 €',
      'invest-1000' => 'Donacion 1000 €',
      'invest-2500' => 'Donacion 2500 €',
      '2-donors' => '2 cofinanciadores',
      '99-donors' => '99 cofinanciadores',
      '200-donors' => '200 cofinanciadores',
      '500-donors' => '500 cofinanciadores'
    );

    public static $links_types = array (
      '#related' => 'Equipo',
      '#goal' => 'Objetivo',
      '#motivation' => 'Motivation',
      'invest' => 'Aportar al proyecto',
      '/participate#messages' => 'Colaboraciones',
      '/participate#supporters' => 'Cofinanciadores',
      '/updates' => 'Novedades',
      '#social-rewards' => 'Retornos colectivos',
      '/' => 'Página principal campaña'
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node and superadmins allowed here
        if( ! Config::isMasterNode($node) || !$user->hasRoleInNode($node, ['superadmin', 'root']) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function editAction($id) {

        $milestone = Model\Milestone::get($id, Config::get('lang'));

        if ($milestone && $this->isPost()) {

            try {
                $milestone->type      = $this->getPost('type');
                $milestone->link      = $this->getPost('link');
                $milestone->description = $this->getPost('description');

                // Image
                if ($this->getPost('image-' . $milestone->image->hash .  '-remove')) {
                    if ($milestone->image instanceof Model\Image) $milestone->image->remove($errors);
                    $milestone->image = null;
                }

                // New image
                if(!empty($_FILES['image']['name'])) {
                    if ($milestone->image instanceof Model\Image) $milestone->image->remove($errors);
                    $milestone->image = $_FILES['image'];
                } else {
                    $milestone->image = $milestone->image->id;
                }

                // Image emojicono
                if ($this->getPost('image-emoji-' . $milestone->image_emoji->hash .  '-remove')) {
                    if ($milestone->image_emoji instanceof Model\Image) $milestone->image_emoji->remove($errors);
                    $milestone->image_emoji = null;
                }

                // New image emojicono
                if(!empty($_FILES['image_emoji']['name'])) {
                    if ($milestone->image_emoji instanceof Model\Image) $milestone->image_emoji->remove($errors);
                    $milestone->image_emoji = $_FILES['image_emoji'];
                } else {
                    $milestone->image_emoji = $milestone->image_emoji->id;
                }

                if ($milestone->save($errors)) {

                    return $this->redirect();
                }
                else {

                    Message::error(implode(', ', $errors));
                }
            } catch(Exception\ModelException $e) {
                Message::error('Milestone not found: ' . $e->getMessage());
            }
        }

        return array(
                'template' => 'admin/milestones/edit',
                'action' => '/admin/milestones/edit/' . $milestone->id,
                'milestone' => $milestone,
                'types' => self::$types,
                'links_types' => self::$links_types
        );
    }


    public function addAction() {

        $milestone = new Milestone;

        if ($milestone && $this->isPost()) {

            try {
                $milestone->type       = $this->getPost('type');
                $milestone->description = $this->getPost('description');

                // New image
                if(!empty($_FILES['image']['name'])) {
                    if ($milestone->image instanceof Model\Image) $milestone->image->remove($errors);
                    $milestone->image = $_FILES['image'];
                } else {
                    $milestone->image = $banner->image->id;
                }

                // New image emojicono
                if(!empty($_FILES['image_emoji']['name'])) {
                    if ($milestone->image_emoji instanceof Model\Image) $milestone->image_emoji->remove($errors);
                    $milestone->image_emoji = $_FILES['image_emoji'];
                } else {
                    $milestone->image_emoji = $banner->image_emoji->id;
                }

                if ($milestone->save($errors)) {
                    return $this->redirect();
                }
                else {

                    Message::error(implode(', ', $errors));
                }
            } catch(Exception\ModelException $e) {
                Message::error('milestone not found: ' . $e->getMessage());
            }
        }

        return array(
                'template' => 'admin/milestones/edit',
                'action' => '/admin/milestones/add',
                'types' => self::$types,
                'links_types' => self::$links_types

        );
    }


    public function listAction() {

        $milestones = Model\Milestone::getAll();

        return array(
                'template' => 'admin/milestones/list',
                'milestones' => $milestones,
                'types' => self::$types,
                'links_types' => self::$links_types
        );
    }


    public function removeAction($id = null) {

        $milestone = Model\Milestone::get($id);

        if ($milestone->dbDelete()) {
            Message::info('Hito eliminado correctamente');
        } else {
            Message::error('No se ha podido eliminar el hito');
        }
        return $this->redirect();

    }

}

