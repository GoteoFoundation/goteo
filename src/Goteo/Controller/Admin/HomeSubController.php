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
 * Elementos de portada
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Model;

class HomeSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'home-lb-list',
    );

    static protected $label = 'home-lb';

    static protected $admin_modules = array(
         'promotes' => '\Goteo\Controller\Admin\PromoteSubController',
         'drops' => '\Goteo\Controller\Admin\CallsSubController',
         'posts' => '\Goteo\Controller\Admin\BlogSubController',
         'sponsors' => '\Goteo\Controller\Admin\SponsorsSubController',
         'stories' => '\Goteo\Controller\Admin\StoriesSubController',
         'news' => '\Goteo\Controller\Admin\NewsSubController',

         // 'categories' => '\Goteo\Controller\Admin\CategoriesSubController',
         'summary' => '\Goteo\Controller\Admin\ProjectsSubController',
         'searcher' => '\Goteo\Controller\Admin\ProjectsSubController',
     );
    static protected $available_types = array(
        'main' => array( 'posts' => 'Entradas de blog',
                         'promotes' => 'Proyectos destacados',
                         'drops' => 'Capital Riego',
                         // 'feed' => 'Actividad reciente',
                         'news' => 'Banner de prensa',
                        ),
        'side' => array( 'searcher' => 'Selector proyectos',
                         // 'categories' => 'Categorias de proyectos',
                         'summary' => 'Resumen proyectos',
                         'sponsors' => 'Patrocinadores'
                        ),
    );

    public function listAction($id = null, $subaction = null) {

        $node = $this->node;

        $errors = array();

        if ($this->isPost()) {

            // instancia
            $item = new Model\Home(array(
                'item' => $this->getPost('item'),
                'type' => $this->getPost('type'),
                'node' => $node,
                'order' => $this->getPost('order'),
                'move' => 'down'
            ));

            if ($item->save($errors)) {
                // ok, sin mensaje porque todo se gestiona en la portada
                // Message::info('Elemento añadido correctamente');
                return $this->redirect();
            } else {
                Message::error(implode('<br />', $errors));
            }
        }

        $central_items = Model\Home::getAll($node, 'main');
        $central_availables = array_diff_key(static::$available_types['main'], $central_items);
        foreach(static::$available_types['main'] as $type => $desc) {
            $item = $central_items[$type];
            if(!is_object($item)) $item = new \stdClass;
            if(isset($item)) {
                $item->desc = $desc;
                $item->item = $type;
            }
            $class = static::$admin_modules[$type];
            if(class_exists($class)) {
                if($class::isAllowed($this->user, $this->node)) {
                    $item->adminUrl = $class::getUrl();
                }
                else {
                    unset($central_availables[$type]);
                }
            }
            // else die("[$class]");
        }

        $side_items = Model\Home::getAll($node, 'side');
        $side_availables = array_diff_key(static::$available_types['side'], $side_items);
        foreach(static::$available_types['side'] as $type => $desc) {
            $item = $side_items[$type];
            if(!is_object($item)) $item = new \stdClass;
            if(isset($item)) {
                $item->desc = $desc;
                $item->item = $type;
            }
            $class = static::$admin_modules[$type];
            if(class_exists($class)) {
                if($class::isAllowed($this->user, $this->node)) {
                    $item->adminUrl = $class::getUrl();
                }
                else {
                    unset($side_availables[$type]);
                }
            }
        }

        return  array(
            'template' => 'admin/home/' . ($this->isMasterNode() ? 'master_node' : ($this->isSuperAdmin() ? 'superadmin_node' : 'admin_node')),
            'central_items' => $central_items,
            /* Para añadir nuevos desde la lista */
            'central_availables' => $central_availables,
            'central_new' => (object) array('node' => $node, 'order' => Model\Home::next($node, 'main'), 'type' => 'main'),

            // laterales
            'side_items' => $side_items,
            'side_availables' => $side_availables,
            'side_new' => (object) array('node' => $node, 'order' => Model\Home::next($node, 'side'), 'type' => 'side'),
        );


    }

    public function upAction($id = null, $subaction = null) {
        $type = ($this->isMasterNode() || empty($subaction)) ? 'main' : $subaction;
        Model\Home::up($id, $this->node, $type);
        return $this->redirect();
    }

    public function downAction($id = null, $subaction = null) {
        $type = ($this->isMasterNode() || empty($subaction)) ? 'main' : $subaction;
        Model\Home::down($id, $this->node, $type);
        return $this->redirect();
    }

    public function removeAction($id = null, $subaction = null) {
        $type = ($this->isMasterNode() || empty($subaction)) ? 'main' : $subaction;
        Model\Home::remove($id, $this->node, $type);
        return $this->redirect();
    }

}

