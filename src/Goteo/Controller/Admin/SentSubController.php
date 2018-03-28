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
 * Historial de envios en el nodo
 */
namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Model\Node;
use Goteo\Library\Feed;
use Goteo\Model\Template;
use Goteo\Model\User;
use Goteo\Model\Mail;
use Goteo\Model\Mail\StatsCollector;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Mail\SenderRecipient;

class SentSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'sent-lb-list',
    );


    static protected $label = 'sent-lb';


    protected $filters = array (
      'user' => '',
      'reply' => '',
      'subject' => '',
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
        $readed_hits = $stats->getEmailOpenedCollector()->non_zero;
        $metric_list = $stats->getAllMetrics();
        $total_metrics = count($metric_list);

        $limit = 50;
        $user_list = [];
        $total = 0;
        // if still in sender
        if($mailing = Sender::getFromMailId($id)) {
            $user_list = SenderRecipient::getList($mailing->id, 'receivers', $this->getGet('pag') * $limit, $limit);
            $total = SenderRecipient::getList($mailing->id, 'receivers', 0, 0, true);
        }
        else {
          // get from mail itself
          $user = User::getByEmail($mail->email);
          $user_list = [
            (object) [
                'email' => $mail->email,
                'user' => $user->id,
                'name' => $user->name,
                'blacklisted' => Mail::checkBlocked($mail->email),
                'status' => $mail->status,
                'error' => $mail->error
            ]
          ];
        }

      $templates = Template::getAllMini();
      return array(
        'template' => 'admin/sent/detail',
        'templates' => $templates,
        'mail' => $mail,
        'sender' => $mailing ? $mailing->id : '',
        'stats' => $stats,
        'readed' => $readed,
        'readed_hits' => $readed_hits,
        'metric_list' => $metric_list,
        'user_list' => $user_list,
        'limit' => $limit,
        'total' => $total
        );
    }

    public function removeblacklistAction($id) {
      $email = $this->getGet('email');

      if(Mail::removeBlocked($email)) {
        Message::info("Quitado de la lista negra: [$email]");
      }
      else {
        Message::info("Ha ocurrido un error al intentar quitar el email [$email] de la lista negra");
      }
      return $this->redirect('/admin/sent/detail/' . $id);
    }

    public function resendAction($id) {
      $email = $this->getGet('email');
      $mail = Mail::get($id);
      $user = User::getByEmail($email);
      if($mail->massive) {
        $recipient = SenderRecipient::getFromMailing(Sender::getFromMailId($id)->id, $email);
        if($recipient->send($errors)) {
          Message::info('Mensaje enviado correctamente');
        } else {
          Message::error('Errors: ' . implode("<br>", $errors));
        }
      }
      else {
        if($email == $mail->email) {
          $errors = [];
          $mail->to = $email;
          $mail->toName = $user->name;
          if($mail->send($errors)) {
            Message::info('Mensaje enviado correctamente');
          } else {
            Message::error('Errors: ' . implode("<br>", $errors));
          }
        }
        else {
          Message::error('This email is not valid for the current Mail');
        }
      }
      return $this->redirect('/admin/sent/detail/' . $id);
      // die("resend id: $id to: $email");
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
        $sent_list = Mail::getSentList($filters, $this->getGet('pag') * $limit, $limit);
        $total = Mail::getSentList($filters, 0, 0, true);

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
