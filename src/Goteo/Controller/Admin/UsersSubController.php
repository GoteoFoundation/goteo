<?php
/**
 * Gestion de usuarios
 */
namespace Goteo\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Library\Template,
    Goteo\Application\Message,
    Goteo\Application\Session,
    Goteo\Application\Config,
    Goteo\Application\Lang,
    Goteo\Model;

class UsersSubController extends AbstractSubController {

    static protected $labels = array (
      'list' => 'Listando',
      'details' => 'Detalles del aporte',
      'update' => 'Cambiando el estado al aporte',
      'add' => 'Creando Usuario',
      'move' => 'Moviendo a otro Nodo el usuario ',
      'execute' => 'Ejecución del cargo',
      'cancel' => 'Cancelando aporte',
      'report' => 'Informe',
      'viewer' => 'Viendo logs',
      'edit' => 'Editando Usuario',
      'translate' => 'Traduciendo Texto',
      'reorder' => 'Ordenando los padrinos en Portada',
      'footer' => 'Ordenando las entradas en el Footer',
      'projects' => 'Informe Impulsores',
      'admins' => 'Asignando administradores del Canal',
      'posts' => 'Entradas de blog en la convocatoria',
      'conf' => 'Configuración de campaña del proyecto',
      'dropconf' => 'Gestionando parte económica de la convocatoria',
      'keywords' => 'Palabras clave',
      'view' => 'Apadrinamientos',
      'info' => 'Información de contacto',
      'send' => 'Comunicación enviada',
      'init' => 'Iniciando un nuevo envío',
      'activate' => 'Iniciando envío',
      'detail' => 'Viendo destinatarios',
      'dates' => 'Fechas del proyecto',
      'accounts' => 'Cuentas del proyecto',
      'images' => 'Imágenes del proyecto',
      'assign' => 'Asignando a una Convocatoria el proyecto',
      'open_tags' => 'Asignando una agrupación al proyecto',
      'rebase' => 'Cambiando Id de proyecto',
      'consultants' => 'Cambiando asesor del proyecto',
      'paypal' => 'Informe PayPal',
      'geoloc' => 'Informe usuarios Localizados',
      'calls' => 'Informe Convocatorias',
      'donors' => 'Informe Donantes',
      'top' => 'Top Cofinanciadores',
      'currencies' => 'Actuales ratios de conversión',
      'preview' => 'Previsualizando Historia',
      'manage' => 'Gestionando Usuario',
      'impersonate' => 'Suplantando al Usuario',
    );


    static protected $label = 'Usuarios';


    protected $filters = array (
      'interest' => '',
      'role' => '',
      'node' => '',
      'id' => '',
      'name' => '',
      'order' => '',
      'project' => '',
      'type' => '',
    );

    public static $manageSubAct = array(
        "ban" => array (
            'sql' => "UPDATE user SET active = 0 WHERE id = :user",
            'log' => "Desactivado"
            ),
        "unban" => array (
            'sql' => "UPDATE user SET active = 1 WHERE id = :user",
            'log' => "Activado"
            ),
        "show" => array (
            'sql' => "UPDATE user SET hide = 0 WHERE id = :user",
            'log' => "Mostrado"
            ),
        "hide" => array (
            'sql' => "UPDATE user SET hide = 1 WHERE id = :user",
            'log' => "Ocultado"
            ),
    );

    /**
     * Some defaults
     */
    public function __construct($node, \Goteo\Model\User $user, Request $request) {
        parent::__construct($node, $user, $request);
        // $this->admins = Model\User::getAdmins();
        // simple list of administrable nodes
        $this->all_nodes = Model\Node::getList();
        $this->nodes = array();
        foreach($user->getAdminNodes() as $node_id => $role) {
            $this->nodes[$node_id] = $this->all_nodes[$node_id];
        }
    }

    /**
     * Overwrite some permissions
     * @inherit
     */
    static public function isAllowed(\Goteo\Model\User $user, $node) {
        // Only central node or superadmins allowed here
        if( ! (Config::isMasterNode($node) || $user->hasRoleInNode($node, ['superadmin', 'root'])) ) return false;
        return parent::isAllowed($user, $node);
    }

    public function moveAction($id = null, $subaction = null) {
        $user = Model\User::get($id);
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
                if (Model\User::query($sql, $values)) {
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
        );    }


    public function impersonateAction($id = null, $subaction = null) {

        $user = Model\User::get($id);

        if(!array_key_exists($user->node, $this->nodes)) {
            Message::error("Your not allowed to edit this user");
            return $this->redirect();
        }

        // vista de acceso a suplantación de usuario
        return array(
            'template' => 'admin/users/impersonate',
            'user'   => $user
        );
    }


    public function manageAction($id = null, $subaction = null) {
        $log = array(
            "checker"      => ["Hecho revisor", "Quitado de revisor"],
            "translator"   => ["Hecho traductor", "Quitado de traductor"],
            "caller"       => ["Hecho convocador", "Quitado de convocador"],
            "admin"        => ["Hecho admin", "Quitado de admin"],
            "vip"          => ["Hecho VIP", "Quitado el VIP"],
            "manager"      => ["Hecho gestor", "Quitado de gestor"]);

        $user = Model\User::get($id);
        $all_roles = Model\User::getRolesList();
        if(!array_key_exists($user->node, $this->nodes)) {
            Message::error("Your not allowed to edit this user");
            return $this->redirect();
        }
        $all_nodes = Model\Node::getList();
        $all_nodes[''] = "(Todos los nodos)";

        $mod = false;
        $text = '';
        if($this->hasPost('add_role')) {
            // check if we can admin this role
            if( $this->user->canAdminRoleInNode($this->getPost('add_role'), $this->getPost('to_node'))) {
                // die('<br>i can ' .$all_roles[$this->getPost('add_role')] .'->'. $this->getPost('to_node'));
                if( $this->user->addRoleToNode($this->getPost('add_role'), $this->getPost('to_node')) ) {
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

                $this->user->delRoleFromNode($this->getGet('del_role'), $this->getGet('from_node')) ) {
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


        if (!empty($sql)) {

            if (Model\User::query($sql, array(':user'=>$id))) {

                $log_text = 'El admin %s ha %s al usuario %s';

                $onNode = Model\Node::get($node);

                // procesos adicionales
                switch ($subaction) {
                    case 'admin':
                        if ($onNode->assign($id)) {
                            Message::info('El nuevo admin se ha añadido a los administradores del nodo <strong>'.$onNode->name.'</strong>.');
                        } else{
                            Message::error('ERROR!!! El nuevo admin no se ha podido añadir a los administradores del nodo <strong>'.$onNode->name.'</strong>. Contactar con el superadmin');
                        }
                        break;

                    case 'noadmin':
                        if ($onNode->unassign($id)) {
                            Message::info('El ex-admin se ha quitado de los administradores del nodo <strong>'.$onNode->name.'</strong>.');
                        } else{
                            Message::error('ERROR!!! El ex-admin no se ha podido quitar de los administradores del nodo <strong>'.$onNode->name.'</strong>. Contactar con el superadmin');
                        }
                        break;

                    case 'translator':
                        // le ponemos todos los idiomas activos (excepto el español)
                        $langs = Lang::listAll('id');
                        //TODO: quitar esto de aqui...
                        unset($langs['es']);
                        foreach($langs as $l) {
                            $sql = "INSERT INTO user_translang (user, lang) VALUES (:user, :lang)";
                            Model\User::query($sql, array(':user' => $id, ':lang' => $l));
                        }
                        break;

                    case 'notranslator':
                        // quitamos los idiomas
                        $sql = "DELETE FROM user_translang WHERE user = :user";
                        Model\User::query($sql, array(':user'=>$id));
                        break;
                }


            } else {

                // mensaje de error y volvemos a la gestion del usuario
                Message::error('Ha FALLADO cuando ha <strong>' . $log_action . '</strong> al usuario <strong>'.$id.'</strong>');
                $log_text = 'Al admin %s le ha <strong>FALLADO</strong> cuando ha %s al usuario %s';

            }


            unset($log);

            return $this->redirect('/admin/users/manage/'.$id);
        }

        $nodes = $this->nodes;
        if($this->user->canAdminRoleInNode('admin')) {
            $nodes[''] = $all_nodes[''];
        }
        $viewData = array(
                'template' => 'admin/users/manage',
                'user'=>$user,
                'nodes' => $nodes,
                'node_roles' => $user->getAllNodeRolesRaw(),
                'new_roles' => Model\User::getRolesList($this->user->getNodeRole($this->node)),
                'langs' => Lang::listAll('name', false)
            );
        // quitamos el español
        unset($viewData['langs']['es']);

        // vista de gestión de usuario
        return $viewData;
    }


    public function editAction($id = null, $subaction = null) {
        $user = Model\User::get($id);

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
            $user = new Model\User();
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
        $users = Model\User::getAll($filters, array_keys($this->nodes));
        $total = Model\User::getAll($filters, array_keys($this->nodes), 0, 0 , true);

        $status = array(
                    'active' => 'Activo',
                    'inactive' => 'Inactivo'
                );
        $interests = Model\User\Interest::getAll();
        $roles = Model\User::getRolesList();
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
        $projects = Model\Invest::projects(true, $node);

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
                'orders' => $orders
        );
    }



    public function process ($action = 'list', $id = null, $filters = array(), $subaction = '') {

        $node = $this->node;
        $nodes = $this->nodes;
        if (!$this->isMasterNode()) {
            // Fuerza el filtro de nodo para que el admin de un nodo no pueda cambiarlo
            // $filters['node'] = $node;
        }

        $errors = array();

        switch ($action)  {

            // aplicar idiomas
            case 'translang':

                if (!$this->hasPost('user')) {
                    Message::error('Hemos perdido de vista al usuario');
                    return $this->redirect();
                } else {
                    $user = $this->getPost('user');
                }

                $sql = "DELETE FROM user_translang WHERE user = :user";
                Model\User::query($sql, array(':user'=>$user));
                $anylang = false;
                if(is_array($this->getPost('langs'))) {
                    foreach ($this->getPost('langs') as $lang) {
                        $sql = "INSERT INTO user_translang (user, lang) VALUES (:user, :lang)";
                        if (Model\User::query($sql, array(':user'=>$user, ':lang'=>$lang))) {
                            $anylang = true;
                        }
                    }
                }
                if (!$anylang) {
                    Message::error('No se ha seleccionado ningún idioma, se ha desactivado la traducción para este usuario!');
                } else {
                    Message::info('Se han aplicado al traductor los idiomas seleccionados');
                }

                return $this->redirect('/admin/users/manage/'.$user);

                break;
        }

    }

}
