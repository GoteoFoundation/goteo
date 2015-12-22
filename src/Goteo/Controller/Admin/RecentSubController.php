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
 * Feed de actividad reciente
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Message;
use Goteo\Application\config,
	Goteo\Library\Feed,
    Goteo\Model;

class RecentSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'recent-lb-list',
    );


    static protected $label = 'recent-lb';

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->getFilters(), $subaction));
    }


    public function process ($action = 'list', $id = null) {

        $node = $this->node;

        $feed = $this->getGet('feed') ? $this->getGet('feed') : 'all';

        $items = Feed::getAll($feed, 'admin', 50, $node);

        return array(
                'folder' => 'recent',
                'file' => $action,
                'feed' => $feed,
                'items' => $items
        );

    }

}
