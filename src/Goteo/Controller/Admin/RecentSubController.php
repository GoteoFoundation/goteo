<?php

namespace Goteo\Controller\Admin;

use Goteo\Application\Message,
	Goteo\Library\Feed,
    Goteo\Model;

class RecentSubController extends AbstractSubController {

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
