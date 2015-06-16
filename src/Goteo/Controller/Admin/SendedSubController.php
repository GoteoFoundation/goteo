<?php

namespace Goteo\Controller\Admin;

use Goteo\Model\Node,
	Goteo\Library\Feed,
	Goteo\Library\Template,
	Goteo\Library\Mail;

class SendedSubController extends AbstractSubController {

    public function process ($action = 'list', $id = null, $filters = array()) {
        $templates = Template::getAllMini();
        $nodes = Node::getList();
        $node = $this->node;

        if ($filters['filtered'] == 'yes'){
            $sended = Mail::getSended($filters, $node);
        } else {
            $sended = array();
        }

        return array(
                'folder' => 'sended',
                'file' => 'list',
                'filters' => $filters,
                'templates' => $templates,
                'nodes' => $nodes,
                'sended' => $sended
        );

    }

}
