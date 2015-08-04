<?php
/**
 * Historial de envios en el nodo
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Model\Node;
use Goteo\Library\Feed;
use Goteo\Model\Template;
use Goteo\Library\Mail;
use Goteo\Library\MailStats;

class SentSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Emails enviados',
    );


    static protected $label = 'Historial envíos';


    protected $filters = array (
      'user' => '',
      'template' => '',
      'node' => '',
      'date_from' => '',
      'date_until' => '',
    );

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function detailAction($id) {
      $mail = Mail::get($id);
      $limit = 25;
      $list = MailStats::getFromMailId($id, $this->getGet('pag') * $limit, $limit);
      $total = MailStats::getFromMailId($id, 0, 0, true);
      return array(
        'template' => 'admin/sent/detail',
        'mail' => $mail,
        'stats_list' => $list,
        'total' => $total,
        'limit' => $limit
        );
    }

    public function listAction() {
        $templates = Template::getAllMini();
        $nodes = array();
        $all_nodes = Node::getList();
        foreach($this->user->getAdminNodes() as $node_id => $role) {
            $nodes[$node_id] = $all_nodes[$node_id];
        }

        $filters = $this->getFilters();
        $limit = 20;
        $sent = Mail::getSentList($filters, $this->node, $this->getGet('pag') * $limit, $limit);
        $total = Mail::getSentList($filters, $this->node, 0, 0, true);

        return array(
                'template' => 'admin/sent/list',
                'filters' => $filters,
                'templates' => $templates,
                'nodes' => $nodes,
                'sent' => $sent,
                'total' => $total,
                'limit' => $limit
        );
    }

}
