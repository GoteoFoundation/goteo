<?php
/**
 * Historial de envios en el nodo
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Model\Node;
use Goteo\Library\Feed;
use Goteo\Model\Template;
use Goteo\Model\Mail;
use Goteo\Model\Mail\StatsCollector;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Mail\SenderRecipient;

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

        $stats = new StatsCollector($mail);
        $readed = $stats->getEmailOpenedCollector()->getPercent();
        $metric_list = $stats->getAllMetrics();
        $total_metrics = count($metric_list);

        $limit = 50;
        $user_list = [];
        $total = 0;
        if($mailing = Sender::getFromMailId($id)) {
            $user_list = SenderRecipient::getList($mailing->id, 'receivers', $this->getGet('pag') * $limit, $limit);
            $total = SenderRecipient::getList($mailing->id, 'receivers', 0, 0, true);
        }

      return array(
        'template' => 'admin/sent/detail',
        'mail' => $mail,
        'stats' => $stats,
        'readed' => $readed,
        'metric_list' => $metric_list,
        'user_list' => $user_list,
        'limit' => $limit,
        'total' => $total
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
        $sent_list = Mail::getSentList($filters, $this->node, $this->getGet('pag') * $limit, $limit);
        $total = Mail::getSentList($filters, $this->node, 0, 0, true);

        return array(
                'template' => 'admin/sent/list',
                'filters' => $filters,
                'templates' => $templates,
                'nodes' => $nodes,
                'sent_list' => $sent_list,
                'total' => $total,
                'limit' => $limit
        );
    }

}
