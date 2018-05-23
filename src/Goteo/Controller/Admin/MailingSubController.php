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
 * Comunicaciones del nodo a sus usuarios
 */
namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\App;
use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Library\Feed;
use Goteo\Library\Text;
use Goteo\Model\Template;
use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Mail\SenderRecipient;
use Goteo\Library\Newsletter;
use Goteo\Application\Lang;
use Goteo\Model;

class MailingSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'mailing-lb-list',
      'edit' => 'mailing-lb-edit',
      'send' => 'mailing-lb-send',
    );


    static protected $label = 'mailing-lb';


    protected $filters = array (
      'project' => '',
      'type' => '',
      'status' => '-1',
      'method' => '',
      'interest' => '',
      'role' => '',
      'name' => '',
      'donant' => '',
      'comlang' => '',
      'langreverse' => '',
      'antiquity' => '',
      'cert' => 0,
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

    public function __construct($node, \Goteo\Model\User $user, Request $request) {
        parent::__construct($node, $user, $request);
        $this->interests = Model\User\Interest::getAll();
        $this->status = Model\Project::status();
        $this->methods = Model\Invest::methods();
        $this->types = array(
            'investor' => 'Cofinanciadores',
            'investor_no_owner' => 'Cofinanciadores no impulsores',
            'owner' => 'Impulsores',
            'no_owner_no_investor' => 'Ni Autores ni cofinanciadores',
            'user' => 'Usuarios'
        );
        $this->roles = array(
            'admin' => 'Administrador',
            'checker' => 'Revisor',
            'translator' => 'Traductor'
        );
        $this->templates = array(
            Template::ADMIN_MESSAGE => 'Base',
            Template::DONORS_WARNING => 'Aviso a los donantes',
            Template::DONORS_REMINDER => 'Recordatorio a los donantes',
            Template::COMMUNICATION => 'Comunicación general',
            Template::TEST => 'Testeo'
        );
        $this->langs = Lang::listAll('object', false);

        $now = new \DateTime(date('Y-m-d'));
        $january1 = new \DateTime(date('Y') . '-01-01');
        $january2 = new \DateTime((date('Y')-1) . '-01-01');
        $january3 = new \DateTime((date('Y')-2) . '-01-01');

        $this->antiquity = [
            '7-0' => 'Últimos 7 días',
            '30-0' => 'Últimos 30 días',
            '365-0' => 'Últimos 365 días',
            ($january1->diff($now)->days + 1) . '-0' => 'Desde 1 enero de ' . date('Y'),
            ($january2->diff($now)->days + 1) .'-' . ($january1->diff($now)->days + 1) => 'El año ' . (date('Y') - 1),
            ($january3->diff($now)->days + 1) .'-' . ($january2->diff($now)->days + 1) => 'El año ' . (date('Y') - 2),
        ];

        $this->certs = [];
        if(class_exists('\Goteo\Model\User\Donor')) {
            $this->certs['confirmed'] = 'Confirmado';
            $this->certs['unconfirmed'] = 'Sin confirmar';
        }
        // print_r($this->antiquity);die;
    }

    public function sendAction() {

        $filters_txt = $this->getReceiversText();

        $comlang = $this->hasPost('lang') ? $this->getPost('lang') : Lang::current();

        // Enviando contenido recibido a destinatarios recibidos
        $receivers = array();

        $subject = trim($this->getPost('subject'));
        $content = trim($this->getPost('content'));
        $template = $this->getPost('template');
        $type = $this->getPost('type');
        Session::store('mailing.subject', $subject);
        Session::store('mailing.content', $content);
        Session::store('mailing.template', $template);
        Session::store('mailing.type', $type);

        $templateId = !empty($this->getPost('template')) ? $template : 11;
        // $content = \str_replace('%SITEURL%', \SITE_URL, $content);

        if(empty($subject) || empty($content)) {
            Message::error('El asunto o el contentido está vacio!');
            return $this->redirect('/admin/mailing/edit');
        }

        if($type === 'md') {
            $content = App::getService('app.md.parser')->text($content);
        }

        // montamos el mailing
        // - se crea un registro de tabla mail
        $mailHandler = new Mail();
        $mailHandler->template = $templateId;
        $mailHandler->subject = $subject;
        $mailHandler->content = $content;
        $mailHandler->node = $node;
        $mailHandler->lang = $comlang;
        $mailHandler->massive = true;
        $errors = [];
        if( !$mailHandler->save($errors) ) throw new ModelException(implode('<br>', $errors));

        $sender = new Sender(['mail' => $mailHandler->id]);
        $errors = [];
        if ( ! $sender->save($errors) ) { //persists in database
            Message::error('Sender saving: ' . implode('<br>', $errors));
            return $this->redirect('/admin/mailing/edit');
        }
        //get the sql to add receivers
        $sql = $this->getReceivers(0, 0, false, $sender->id);

        $this->debug("SQL receivers", ['sql' => $sql, $sender, $this->user]);

        // add subscribers
        Sender::addSubscribersFromSQL($sql);

        // Evento Feed
        $log = new Feed();
        $log->populate(Text::sys('feed-admin-massive-subject'), '/admin/mailing',
            Text::sys('feed-admin-massive', [
                '%USER%' =>  Feed::item('user', $this->user->name, $this->user->id),
                '%TYPE%' =>  Feed::item('relevant', Text::sys('feed-admin-massive-communication')),
                '%TO%' => $filters_txt
            ]))
            ->doAdmin('admin');

        Session::del('mailing.subject');
        Session::del('mailing.content');
        Session::del('mailing.type');
        Session::del('mailing.template');
        Session::del('mailing.removed_receivers');

        return $this->redirect('/admin/mailing/detail/' . $sender->id);
    }

    public function detailAction($id) {
        $sender = Sender::get($id);
        $total = SenderRecipient::getList($sender->id, 'receivers', 0, 0, true);
        return array(
                'template'    => 'admin/mailing/detail',
                'sender'      => $sender,
                'mail'      => $sender->mail,
                'subject'     => $sender->getMail()->subject,
                'total'       => $total,
                'link'        => $sender->getLink(),
                'filters_txt' => $this->getReceiversText()
        );
    }

    public function editAction() {
        $filters_txt = $this->getReceiversText();
        // si no hay destinatarios, salta a la lista con mensaje de error
        $limit = 25;
        $receivers = $this->getReceivers($this->getGet('pag') * $limit, $limit);
        $total = $this->getReceivers(0, 0, true);
        if (empty($receivers)) {
            Message::error('No se han encontrado destinatarios para ' . $filters_txt);

            return $this->redirect('/admin/mailing/list');
        }

        // si hay, mostramos el formulario de envio
        return array(
                'template'    => 'admin/mailing/edit',
                'templates'    => $this->templates,
                'languages' => Lang::listAll('name', false),
                'type' => Session::get('mailing.type', 'md'),
                'receivers' => $receivers,
                'removed_receivers' => Session::get('mailing.removed_receivers', []),
                'subject' => Session::get('mailing.subject'),
                'content' => Session::get('mailing.content'),
                'templateId' => Session::get('mailing.template'),
                'total' => $total,
                'limit' => $limit,
                'filters_txt' => $filters_txt
        );
    }

    /**
     * JSON action
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function receiverAction($id, $subaction) {
        $removed_receivers = Session::get('mailing.removed_receivers', []);
        if($subaction === 'add') {
            unset($removed_receivers[$id]);
        }
        elseif($subaction === 'remove') {
            $removed_receivers[$id] = $id;
        }
        Session::store('mailing.removed_receivers', $removed_receivers);

        return $this->jsonResponse(['id' => $id, 'active' => !isset($removed_receivers[$id])]);
    }

    public function get_template_contentAction($id) {
        $lang = $this->getGet('hl');
        $template = Template::get($id, $lang);

        return $this->jsonResponse(['title' => $template->title, 'text' => $template->text, 'type' => $template->type]);
    }

    public function copyAction($id) {
        if($mail = Mail::get($id)) {
            Session::store('mailing.subject', $mail->subject);
            Session::store('mailing.content', $mail->content);
            Session::store('mailing.template', $mail->template);
            Session::store('mailing.type', 'html');
            return array(
                'template'    => 'admin/mailing/list',
                'interests' => $this->interests,
                'subject'   => $mail->subject,
                'status'    => $this->status,
                'methods'   => $this->methods,
                'types'     => $this->types,
                'roles'     => $this->roles,
                'langs'     => $this->langs,
                'filters'   => $this->getFilters()
            );
        }
        else {
            Message::error("Not found mail ID [$id]");
        }
        return $this->redirect();

    }

    public function listAction() {

        return array(
                'template'    => 'admin/mailing/list',
                'interests' => $this->interests,
                'status'    => $this->status,
                'methods'   => $this->methods,
                'types'     => $this->types,
                'roles'     => $this->roles,
                'langs'     => $this->langs,
                'antiquity' => $this->antiquity,
                'certs'     => $this->certs,
                'filters'   => $this->getFilters()
        );

    }


    private function getReceiversText() {
        $filters = $this->getFilters();
        $interests = $this->interests;
        $status = $this->status;
        $methods = $this->methods;
        $types = $this->types;
        $roles = $this->roles;
        $langs = $this->langs;
        $antiquity = $this->antiquity;
        $certs = $this->certs;

        $investor_owner = in_array($filters['type'], ['investor', 'owner']);

        $filters_txt = 'los <strong>' . $types[$filters['type']] . '</strong> ';

        if (!empty($filters['project']) && $investor_owner) {
            $filters_txt .= 'de proyectos que su nombre contenga <strong>\'' . $filters['project'] . '\'</strong> ';
        } elseif (empty($filters['project']) && $investor_owner) {
            $filters_txt .= 'de cualquier proyecto ';
        }

        if (isset($filters['status']) && $filters['status'] > -1 && $investor_owner) {
            $filters_txt .= 'en estado <strong>' . $status[$filters['status']] . '</strong> ';
        } elseif ($filters['status'] < 0 && $investor_owner) {
            $filters_txt .= 'en <strong>cualquier estado</strong> ';
        }

        if ($filters['type'] == 'investor') {
            if (!empty($filters['method']) && $investor_owner) {
                $filters_txt .= 'mediante <strong>' . $methods[$filters['method']] . '</strong> ';
            } elseif (empty($filters['method']) && $investor_owner) {
                $filters_txt .= 'mediante <strong>cualquier metodo</strong> ';
            }
            if (!empty($filters['antiquity']) && $investor_owner) {
                $filters_txt .= 'de fecha <strong>' . $antiquity[$filters['antiquity']] . '</strong> ';
            } elseif (empty($filters['antiquity']) && $investor_owner) {
                $filters_txt .= 'en <strong>cualquier fecha</strong> ';
            }
            if(class_exists('\Goteo\Model\User\Donor')) {
                if (!empty($filters['cert']) && $investor_owner) {
                    $filters_txt .= 'con certificado <strong>' . $certs[$filters['cert']] . '</strong> ';
                } elseif (empty($filters['cert']) && $investor_owner) {
                    $filters_txt .= 'con <strong>certificado o sin él</strong> ';
                }
            }
        }

        if ($filters['interest'] == 15) {
            $filters_txt .= 'del grupo de testeo ';
        } elseif (!empty($filters['interest'])) {
            $filters_txt .= 'interesados en fin <strong>' . $interests[$filters['interest']] . '</strong> ';
        }

        if (!empty($filters['role'])) {
            $filters_txt .= 'que sean <strong>' . $roles[$filters['role']] . '</strong> ';
        }

        if (!empty($filters['name'])) {
            $filters_txt .= 'que su nombre o email contenga <strong>\'' . $filters['name'] . '\'</strong> ';
        }

        if (!empty($filters['comlang'])) {
            $filters_txt .= 'con idioma de preferencia ';
            if($filters['langreverse']) {
                $filters_txt .= 'que NO sea ';
            }
            $filters_txt .= '<strong>' . $langs[$filters['comlang']]->short . '</strong> ';
        }

        return $filters_txt;
    }

    private function getReceivers($offset = 0, $limit = 10, $count = false, $sender_id = null) {

        $filters = $this->getFilters();
        $interests = $this->interests;
        $status = $this->status;
        $methods = $this->methods;
        $types = $this->types;
        $roles = $this->roles;
        $langs = $this->langs;

        $receivers = array();

        $values = array();
        $sqlFields  = '';
        $sqlInner  = '';
        $sqlFilter = '';


        $offset = (int) $offset;
        $limit = (int) $limit;

        // cargamos los destiantarios
        //----------------------------
        // por tipo de usuario
        switch ($filters['type']) {
            case 'investor':
                $sqlInner .= "INNER JOIN invest
                        ON invest.user = user.id
                        AND invest.status IN(0, 1, 3, 4, 6)
                    INNER JOIN project
                        ON project.id = invest.project
                        ";
                $sqlFields .= ", project.name as project";
                $sqlFields .= ", project.id as projectId";
                break;
            case 'owner':
                $sqlInner .= "INNER JOIN project
                        ON project.owner = user.id
                        ";
                $sqlFields .= ", project.name as project";
                $sqlFields .= ", project.id as projectId";
        }

        if (!empty($filters['project']) && !empty($sqlInner)) {
            $sqlFilter .= " AND project.name LIKE (:project) ";
            $values[':project'] = '%'.$filters['project'].'%';
        }

        if (isset($filters['status']) && $filters['status'] > -1 && !empty($sqlInner)) {
            $sqlFilter .= "AND project.status = :status ";
            $values[':status'] = $filters['status'];
        }

        if($filters['type'] == 'no_owner_no_investor')
        {
            $sqlFilter .= "AND user.id NOT IN (SELECT user FROM invest WHERE invest.status IN (0, 1, 3, 4, 6)) AND user.id NOT IN (SELECT owner FROM project)";
        }

        if($filters['type'] == 'investor_no_owner')
        {
            $sqlFilter .= "AND user.id IN (SELECT user FROM invest WHERE invest.status IN (0, 1, 3, 4, 6)) AND user.id NOT IN (SELECT owner FROM project) ";
        }

        if ($filters['type'] == 'investor' && !empty($sqlInner)) {
            if (!empty($filters['method'])) {
                $sqlFilter .= "AND invest.method = :method ";
                $values[':method'] = $filters['method'];
            }
            if (!empty($filters['antiquity'])) {
                $from = (int)strtok($filters['antiquity'], '-');
                $to = (int)strtok('-');
                $sqlFilter .= "AND invest.invested BETWEEN DATE_SUB(NOW(), INTERVAL :from DAY) AND DATE_SUB(NOW(), INTERVAL :to DAY) ";
                $values[':from'] = $from;
                $values[':to'] = $to;
            }
            if (!empty($filters['cert']) && class_exists('\Goteo\Model\User\Donor')) {
                $sqlInner .= 'LEFT JOIN donor ON donor.user = user.id ';
                if($filters['cert'] == 'unconfirmed') {
                    $sqlFilter .= " AND (donor.confirmed = 0 OR ISNULL(donor.confirmed)) ";
                }
                if($filters['cert'] == 'confirmed') {
                    $sqlFilter .= " AND donor.confirmed = 1 ";
                }
            }
        }

        if (!empty($filters['interest'])) {
            $sqlInner .= "INNER JOIN user_interest
                    ON user_interest.user = user.id
                    AND user_interest.interest = :interest
                    ";
            $values[':interest'] = $filters['interest'];
        }

        if (!empty($filters['role'])) {
            $sqlInner .= "INNER JOIN user_role
                    ON user_role.user_id = user.id
                    AND user_role.role_id = :role
                    ";
            $values[':role'] = $filters['role'];
        }

        if (!empty($filters['name'])) {
            $sqlFilter .= " AND ( user.name LIKE :name OR user.email LIKE :name ) ";
            $values[':name'] = '%'.$filters['name'].'%';
        }

        if (!$this->isMasterNode()) {
            $sqlFilter .= " AND user.node = :node";
            $values[':node'] = $node;
            if (!empty($sqlInner)) {
                $sqlFilter .= " AND project.node = :node";
            }
        }

        if (!empty($filters['comlang'])) {
            $sqlInner .= "LEFT JOIN user_prefer
                    ON user_prefer.user = user.id";
            $f = "user_prefer.comlang=:comlang OR (ISNULL(user_prefer.comlang) AND user.lang=:comlang)";
            if($filters['langreverse']) {
                $f = "user_prefer.comlang!=:comlang OR (ISNULL(user_prefer.comlang) AND user.lang!=:comlang)";
            }
            $sqlFilter .= "AND ($f)";
            $values[':comlang'] = $filters['comlang'];
        }


        // return SQL suitable for automatic adding into Sender::addSubscribersFromSQL
        // `mailing`, `user`, `name`, `email`
        if(is_numeric($sender_id)) {
            $removed_receivers = Session::get('mailing.removed_receivers', []);
            if($removed_receivers) {
                $sqlFilter .= " AND user.id NOT IN ('" . implode("','", $removed_receivers) . "')";
            }
            return \sqldbg("SELECT
                    $sender_id as mailing,
                    user.id as user,
                    user.name as name,
                    user.email as email
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC", $values);
        }
        // Return total count for pagination
        if($count) {
            $sql = "SELECT COUNT(DISTINCT(user.id)) FROM user $sqlInner WHERE user.active = 1 $sqlFilter";
            // die( \sqldbg($sql, $values) );
            return (int) Model\User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as user,
                    user.name as name,
                    user.email as email
                    $sqlFields
                FROM user
                $sqlInner
                WHERE user.active = 1
                $sqlFilter
                GROUP BY user.id
                ORDER BY user.name ASC
                LIMIT $offset, $limit
                ";

        // die( \sqldbg($sql, $values) );

        if ($query = Model\User::query($sql, $values)) {
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $receiver) {
                $receiver->id = $receiver->user;
                $receivers[$receiver->id] = $receiver;
            }
        } else {
            throw new ModelException('Fallo el SQL!!!!! <br />' . $sql . '<pre>'.print_r($values, true).'</pre>');
        }

        return $receivers;
    }
}
