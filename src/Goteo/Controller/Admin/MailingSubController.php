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
use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Library\Feed;
use Goteo\Model\Template;
use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;
use Goteo\Model\Mail\SenderRecipient;
use Goteo\Library\Newsletter;
use Goteo\Application\Lang;
use Goteo\Model;

class MailingSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Seleccionando destinatarios',
      'edit' => 'Escribiendo contenido',
      'send' => 'Comunicación enviada',
    );


    static protected $label = 'Comunicaciones';


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
            'owner' => 'Autores',
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
            Template::TEST => 'Testeo'
        );
        $this->langs = Lang::listAll('object', false);

        // // una variable de sesion para mantener los datos de todo esto
        // if (!isset($_SESSION['mailing'])) {
        //     $_SESSION['mailing'] = array();
        // }

    }

    public function sendAction() {

        $filters_txt = $this->getReceiversText();

        $comlang = $this->hasPost('lang') ? $this->getPost('lang') : Lang::current();

        // Enviando contenido recibido a destinatarios recibidos
        $receivers = array();

        $subject = trim($this->getPost('subject'));
        $content = trim($this->getPost('content'));
        $template = $this->getPost('template');
        Session::store('mailing.subject', $subject);
        Session::store('mailing.content', $content);
        Session::store('mailing.template', $template);

        $templateId = !empty($this->getPost('template')) ? $template : 11;
        $content = \str_replace('%SITEURL%', \SITE_URL, $content);

        if(empty($subject) || empty($content)) {
            Message::error('El asunto o el contentido está vacio!');
            return $this->redirect('/admin/mailing/edit');
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

        // add subscribers
        Sender::addSubscribersFromSQL($sql);

        if ($sender->setActive(true)->active)  {
            $ok = true;
            // Evento Feed
            $log = new Feed();
            $log->populate('comunicación masiva a usuarios (admin)', '/admin/mailing',
                \vsprintf("El admin %s ha iniciado una %s a %s", array(
                Feed::item('user', $this->user->name, $this->user->id),
                Feed::item('relevant', 'Comunicacion masiva'),
                $filters_txt
            )));
            $log->doAdmin('admin');
            unset($log);
        } else {
            Message::error('Atención, no se ha activado el envío!');
            $ok = false;
            // Evento Feed
            $log = new Feed();
            $log->populate('comunicación masiva a usuarios (admin)', '/admin/mailing',
                \vsprintf("El admin %s le ha %s una %s a %s", array(
                Feed::item('user', $this->user->name, $this->user->id),
                Feed::item('relevant', 'fallado'),
                Feed::item('relevant', 'Comunicacion masiva'),
                $filters_txt
            )));
            $log->doAdmin('admin');
            unset($log);
        }

        Session::del('mailing.subject');
        Session::del('mailing.content');
        Session::del('mailing.template');
        Session::del('mailing.removed_receivers');

        return $this->redirect('/admin/mailing/detail/' . $sender->id);
    }

    public function detailAction($id) {
        $sender = Sender::get($id);
        $total = SenderRecipient::getList($sender->id, 'receivers', 0, 0, true);
        return array(
                'template'    => 'admin/mailing/detail',
                'sender'      => $id,
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
        $Template = Template::get($id);

        return $this->jsonResponse(['title' => $Template->title, 'text' => $Template->text]);
    }

    public function copyAction($id) {
        if($mail = Mail::get($id)) {
            Session::store('mailing.subject', $mail->subject);
            Session::store('mailing.content', $mail->content);
            Session::store('mailing.template', $mail->template);
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
            $filters_txt .= 'en cualquier estado ';
        }

        if ($filters['type'] == 'investor') {
            if (!empty($filters['method']) && $investor_owner) {
                $filters_txt .= 'mediante <strong>' . $methods[$filters['method']] . '</strong> ';
            } elseif (empty($filters['method']) && $investor_owner) {
                $filters_txt .= 'mediante cualquier metodo ';
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
            $filters_txt .= 'con idioma preferencia <strong>' . $langs[$filters['comlang']]->short . '</strong> ';
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
                        AND (invest.status = 0 OR invest.status = 1 OR invest.status = 3 OR invest.status = 4)
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

        if ($filters['type'] == 'investor') {
            if (!empty($filters['method']) && !empty($sqlInner)) {
                $sqlFilter .= "AND invest.method = :method ";
                $values[':method'] = $filters['method'];
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
            $sqlInner .= "INNER JOIN user_prefer
                    ON user_prefer.user = user.id
                    AND user_prefer.comlang = :comlang
                    ";
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
            return (int) Model\User::query($sql, $values)->fetchColumn();
        }

        $sql = "SELECT
                    user.id as id,
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
                $receivers[$receiver->id] = $receiver;
            }
        } else {
            throw new ModelException('Fallo el SQL!!!!! <br />' . $sql . '<pre>'.print_r($values, true).'</pre>');
        }

        return $receivers;
    }
}
