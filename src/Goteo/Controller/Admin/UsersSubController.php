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
 * Gestion de usuarios
 */
namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Model\Template;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Model\User\UserLocation;
use Goteo\Model\User;
use Goteo\Model\Invest;
use Goteo\Model\Node;

class UsersSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'users-lb-list',
      'add' => 'users-lb-add',
      'move' => 'users-lb-move',
      'edit' => 'users-lb-edit',
      'manage' => 'users-lb-manage',
      'impersonate' => 'users-lb-impersonate',
    );


    static protected $label = 'users-lb';


    protected $filters = array (
      'interest' => '',
      'role' => '',
      'node' => '',
      'id' => '',
      'global' => '',
      'order' => '',
      'project' => '',
      'type' => '',
    );

    /**
     * Some defaults
     */
    public function __construct($node, User $user, Request $request) {
        parent::__construct($node, $user, $request);
        // $this->admins = User::getAdmins();
        // simple list of administrable nodes
        $this->all_nodes = Node::getList();
        $this->nodes = array();
        foreach($user->getAdminNodes() as $node_id => $role) {
            $this->nodes[$node_id] = $this->all_nodes[$node_id];
        }
    }

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function moveAction($id = null, $subaction = null) {
        $user = User::get($id);
        $post_node = $this->getPost('node');
        if(!array_key_exists($user->node, $this->nodes)) {
            Message::error("Your not allowed to move this user");
            return $this->redirect();
        }
        if ($this->isPost()) {
            if(!array_key_exists($post_node, $this->nodes)) {
                Message::error("Your not allowed to move this user to this node");
                return $this->redirect();
            }
            $values = array(':id' => $id, ':node' => $post_node);
            try {
                $sql = "UPDATE user SET node = :node WHERE id = :id";
                if (User::query($sql, $values)) {
                    $log_text = 'El admin %s ha <span class="red">movido</span> el usuario %s al nodo %s';
                } else {
                    $log_text = 'Al admin %s le ha <span class="red">fallado al mover</span> el usuario %s al nodo %s';
                }
                // Evento Feed
                $log = new Feed();
                $log->setTarget($user->id, 'user');
                $log->populate('User cambiado de nodo (admin)', self::getUrl(),
                    \vsprintf($log_text, array(
                        Feed::item('user', $this->user->name, $this->user->id),
                        Feed::item('user', $user->name, $user->id),
                        Feed::item('user', $this->nodes[$post_node])
                )));
                Message::error($log->html);
                $log->doAdmin('user');
                unset($log);

                return $this->redirect();

            } catch(\PDOException $e) {
                Message::error("Ha fallado! " . $e->getMessage());
            }
        }

        // vista de acceso a suplantación de usuario
        return array(
                'template' => 'admin/users/move',
                'user'   => $user
        );
    }


    public function impersonateAction($id = null, $subaction = null) {

        $user = User::get($id);

        if(!array_key_exists($user->node, $this->nodes)) {
            Message::error("Your not allowed to edit this user");
            return $this->redirect();
        }

        if($this->isPost()) {
            if(Session::exists('shadowed_by')) {
                Message::error('Ya estas suplantando un usuario, por favor cierra primero la sesión!');
                return $this->redirect();
            }

            Session::onSessionDestroyed(function () use ($user) {
                Message::error('User <strong>' . $this->user->name . ' ('. $this->user->id. ')</strong> converted to <strong>' . $user->name . ' ('. $user->id. ')</strong>');
            });
            Session::destroy();
            Session::setUser($user);
            // Session::store('shadowed_by', [$this->user->id, $this->user->name, self::getUrl('impersonate', $id)]);
            Session::store('shadowed_by', [$this->user->id, $this->user->name, $this->getReferer() ? $this->getReferer() : self::getUrl('impersonate', $id)]);
            // Evento Feed
            $log = new Feed();
            $log->setTarget(Session::getUserId(), 'user');
            $log->populate('Suplantación usuario (admin)', self::getUrl(), \vsprintf('El admin %s ha %s al usuario %s', array(
                Feed::item('user', $this->user->name, $this->user->id),
                Feed::item('relevant', 'Suplantado'),
                Feed::item('user', Session::getUser()->name, Session::getUserId())
            )));
            $log->doAdmin('user');
            $referer = $this->getReferer();
            if(!$referer || strpos($referer, '/admin')) $referer = '/dashboard';
            return $this->redirect($referer);
        }

        // vista de acceso a suplantación de usuario
        return array(
            'template' => 'admin/users/impersonate',
            'user'   => $user
        );
    }


    public function manageAction($id = null, $subaction = null) {

        $user = User::get($id);
        $all_roles = User::getRolesList();
        if(!array_key_exists($user->node, $this->nodes)) {
            Message::error("You're not allowed to edit this user");
            return $this->redirect();
        }
        $all_nodes = Node::getList();
        $all_nodes[''] = "(Todos los nodos)";

        $mod = false;
        $text = '';
        if($this->hasPost('add_role')) {
            // check if we can admin this role
            if( $this->user->canAdminRoleInNode($this->getPost('add_role'), $this->getPost('to_node'))) {
                // die('<br>i can ' .$all_roles[$this->getPost('add_role')] .'->'. $this->getPost('to_node'));
                if( $user->addRoleToNode($this->getPost('add_role'), $this->getPost('to_node')) ) {
                    $text = sprintf("Añadido rol de <strong>%s</strong> para el nodo <strong>%s</strong>",
                                    $all_roles[$this->getPost('add_role')],
                                    $all_nodes[$this->getPost('to_node')]);
                    $mod = true;
                }
            }
            else {
                $text = sprintf("Error añadiendo rol de <strong>%s</strong> para el nodo <strong>%s</strong>",
                    $all_roles[$this->getPost('add_role')],
                    $all_nodes[$this->getPost('to_node')]);
            }
        }
        elseif($this->hasGet('del_role')) {
            // check if we can admin this role
            if( $this->user->canAdminRoleInNode($this->getGet('del_role'), $this->getGet('from_node')) &&
                // TODO: Do not let remove myself

                $user->delRoleFromNode($this->getGet('del_role'), $this->getGet('from_node')) ) {
                $text = sprintf("Quitado rol de <strong>%s</strong> para el nodo <strong>%s</strong>",
                                $all_roles[$this->getGet('del_role')],
                                $all_nodes[$this->getGet('from_node')]);
                $mod = true;
            }
            else {
                $text = sprintf("Error quitando rol de <strong>%s</strong> para el nodo <strong>%s</strong>",
                            $all_roles[$this->getGet('del_role')],
                            $all_nodes[$this->getGet('from_node')]);
            }
        }
        elseif($subaction) {
            $actions = array(
                'ban' =>   ['active', 0, 'Desactivado'],
                'unban' => ['active', 1, 'Activado'],
                'show' =>  ['hide', 0, 'Mostrado'],
                'hide' =>  ['hide', 1, 'Ocultado']
            );
            if($p = $actions[$subaction]) {
                if(User::setProperty($user->id, $p[1], $p[0])) {
                    $text = $p[2] . ' correctamente';
                    $mod = true;
                }
                else {
                    $text = $p[2] . ' erróneamente!';
                }
            }
            elseif($subaction === 'translang') {
                $sql = "DELETE FROM user_translang WHERE user = :user";
                User::query($sql, array(':user' => $id));
                $anylang = false;
                if(is_array($this->getPost('langs'))) {
                    foreach ($this->getPost('langs') as $lang) {
                        $sql = "INSERT INTO user_translang (user, lang) VALUES (:user, :lang)";
                        if (User::query($sql, array(':user' => $id, ':lang' => $lang))) {
                            $anylang = true;
                        }
                    }
                }
                if (!$anylang) {
                    Message::error('No se ha seleccionado ningún idioma, se ha desactivado la traducción para este usuario!');
                } else {
                    Message::info('Se han aplicado al traductor los idiomas seleccionados');
                }
                return $this->redirect(self::getUrl('manage', $id));
            }
        }
        if($text) {
            if($mod) Message::info($text);
            else     Message::error($text);

            $log_text = 'El admin %s ha %s al usuario %s';
            // Evento Feed
            $log = new Feed();
            $log->setTarget($user->id, 'user');
            $log->populate('Operación sobre usuario (admin)', self::getUrl('manage', $id),
                sprintf($log_text, Feed::item('user', $this->user->name, $this->user->id),
                                   Feed::item('relevant', $text),
                                   Feed::item('user', $user->name, $user->id)
                       ));
            $log->doAdmin('user');
            return $this->redirect(self::getUrl('manage', $id));
        }

        $nodes = $this->nodes;
        if($this->user->canAdminRoleInNode('admin')) {
            $nodes[''] = $all_nodes[''];
        }
        $viewData = array(
                'template' => 'admin/users/manage',
                'user'=>$user,
                'location'=> UserLocation::get($user),
                'poolAmount' => $user->getPool()->getAmount(),
                'nodes' => $nodes,
                'all_nodes' => $all_nodes,
                'node_roles' => $user->getAllNodeRolesRaw(),
                'new_roles' => User::getRolesList($this->user->getNodeRole($this->node)),
                'langs' => Lang::listAll('name', false)
            );
        // quitamos el español
        unset($viewData['langs']['es']);

        // vista de gestión de usuario
        return $viewData;
    }


    public function editAction($id = null, $subaction = null) {
        $user = User::get($id);

        if(!array_key_exists($user->node, $this->nodes)) {
            Message::error("Your not allowed to edit this user");
            return $this->redirect();
        }

        // si llega post: actualizamos
        if ($this->isPost()) {
            $tocado = array();
            // para crear se usa el mismo método save del modelo, hay que montar el objeto
            if (!empty($this->getPost('email'))) {
                $user->email = $this->getPost('email');
                $tocado[] = 'el email';
            }
            if (!empty($this->getPost('password'))) {
                $user->password = $this->getPost('password');
                $tocado[] = 'la contraseña';
            }

            if(!empty($tocado) && $user->update($errors)) {

                // Evento Feed
                $log = new Feed();
                $log->setTarget($user->id, 'user');
                $log->populate('Operación sobre usuario (admin)', self::getUrl(), \vsprintf('El admin %s ha %s del usuario %s', array(
                    Feed::item('user', $this->user->name, $this->user->id),
                    Feed::item('relevant', 'Tocado ' . implode (' y ', $tocado)),
                    Feed::item('user', $user->name, $user->id)
                )));
                $log->doAdmin('user');
                unset($log);

                // mensaje de ok y volvemos a la lista de usuarios
                Message::info('Datos actualizados');
                return $this->redirect();

            } else {
                // si hay algun error volvemos a poner los datos en el formulario
                $data = $_POST;
                Message::error(implode('<br />', $errors));
            }
        }

        // vista de editar usuario
        return array(
                'template' => 'admin/users/edit',
                'user'=>$user,
                'data'=>$data
        );
    }


    public function addAction($id = null, $subaction = null) {

        // si llega post: creamos
        if ($this->isPost()) {

            // para crear se usa el mismo método save del modelo, hay que montar el objeto
            $user = new User();
            $user->userid = $this->getPost('userid');
            $user->name = $this->getPost('name');
            $user->email = $this->getPost('email');
            $user->password = $this->getPost('password');
            $user->node = $this->getPost('node') ? $this->getPost('node') : $node;
            //TODO: check permissions on node change
            $user->save($errors);

            if(empty($errors)) {
              // mensaje de ok y volvemos a la lista de usuarios
              Message::info(Text::get('user-register-success'));
              return $this->redirect('/admin/users/manage/'.$user->id);
            } else {
                // si hay algun error volvemos a poner los datos en el formulario
                $data = $this->getPost()->all();
                Message::error(implode('<br />', $errors));
            }
        }

        // vista de crear usuario
        return array(
                'template' => 'admin/users/add',
                'data' => $data
        );
    }


    public function listAction($id = null, $subaction = null) {
        $filters = $this->getFilters();
        $limit = 20;
        $users = User::getList($filters, array_keys($this->nodes), $this->getGet('pag') * $limit, $limit);
        $total = User::getList($filters, array_keys($this->nodes), 0, 0 , true);

        $status = array(
                    'active' => 'Activo',
                    'inactive' => 'Inactivo'
                );
        $interests = User\Interest::getAll();
        $roles = User::getRolesList();
        $roles['user'] = 'Solo usuario';
        $types = array(
            'creators' => 'Impulsores', // que tienen algun proyecto
            'investors' => 'Cofinanciadores', // que han aportado a algun proyecto en campaña, financiado, archivado o caso de éxito
            'supporters' => 'Colaboradores', // que han enviado algun mensaje en respuesta a un mensaje de colaboración
            'consultants' => 'Asesores'
            // hay demasiados de estos... 'lurkers' => 'Mirones'
        );
        $orders = array(
            'created' => 'Fecha de alta',
            'name' => 'Alias',
            'id' => 'User',
            'amount' => 'Cantidad',
            'projects' => 'Proyectos'
        );
        // proyectos con aportes válidos
        $projects = Invest::projects(true, $this->node);

        // print_r($users);die;
        return array(
                'template' => 'admin/users/list',
                'users' => $users,
                'total' => $total,
                'filters' => $filters,
                'status' => $status,
                'interests' => $interests,
                'roles' => $roles,
                'types' => $types,
                'projects' => $projects,
                'orders' => $orders,
                'limit' => $limit
        );
    }

}
