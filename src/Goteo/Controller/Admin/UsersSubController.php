<?php

namespace Goteo\Controller\Admin;

use Goteo\Library\Text,
	Goteo\Library\Feed,
	Goteo\Library\Template,
    Goteo\Application\Message,
    Goteo\Application\Session,
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


    public function moveAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('move', $id, $this->filters, $subaction));
    }


    public function impersonateAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('impersonate', $id, $this->filters, $subaction));
    }


    public function manageAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('manage', $id, $this->filters, $subaction));
    }


    public function editAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('edit', $id, $this->filters, $subaction));
    }


    public function addAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('add', $id, $this->filters, $subaction));
    }


    public function listAction($id = null, $subaction = null) {
        // Action code should go here instead of all in one process funcion
        return call_user_func_array(array($this, 'process'), array('list', $id, $this->filters, $subaction));
    }


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
        "checker" => array (
            'sql' => "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'checker')",
            'log' => "Hecho revisor"
            ),
        "nochecker" => array (
            'sql' => "DELETE FROM user_role WHERE role_id = 'checker' AND user_id = :user",
            'log' => "Quitado de revisor"
            ),
        "translator" => array (
            'sql' => "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'translator')",
            'log' => "Hecho traductor"
            ),
        "notranslator" => array (
            'sql' => "DELETE FROM user_role WHERE role_id = 'translator' AND user_id = :user",
            'log' => "Quitado de traductor"
            ),
        "caller" => array (
            'sql' => "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'caller')",
            'log' => "Hecho convocador"
            ),
        "nocaller" => array (
            'sql' => "DELETE FROM user_role WHERE role_id = 'caller' AND user_id = :user",
            'log' => "Quitado de convocador"
            ),
        "admin" => array (
            'sql' => "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'admin')",
            'log' => "Hecho admin"
            ),
        "noadmin" => array (
            'sql' => "DELETE FROM user_role WHERE role_id = 'admin' AND user_id = :user",
            'log' => "Quitado de admin"
            ),
        "vip" => array (
            'sql' => "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'vip')",
            'log' => "Hecho VIP"
            ),
        "novip" => array (
            'sql' => "DELETE FROM user_role WHERE role_id = 'vip' AND user_id = :user",
            'log' => "Quitado el VIP"
            ),
        "manager" => array (
            'sql' => "REPLACE INTO user_role (user_id, role_id) VALUES (:user, 'manager')",
            'log' => "Hecho gestor"
            ),
        "nomanager" => array (
            'sql' => "DELETE FROM user_role WHERE role_id = 'manager' AND user_id = :user",
            'log' => "Quitado de gestor"
            )
    );


    public function process ($action = 'list', $id = null, $filters = array(), $subaction = '') {

        // multiples usos
        $nodes = Model\Node::getList();
        $admin_subnode = false;

        $node = $this->node;

        if (!$this->isMasterNode()) {
            // Fuerza el filtro de nodo para que el admin de un nodo no pueda cambiarlo
            $filters['node'] = $node;
            $admin_subnode = true;
        }

        $errors = array();

        switch ($action)  {
            case 'add':

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
                        'folder' => 'users',
                        'file' => 'add',
                        'data'=>$data,
                        'nodes' => $nodes
                );

                break;
            case 'edit':

                $user = Model\User::get($id);

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
                        $log->populate('Operación sobre usuario (admin)', '/admin/users', \vsprintf('El admin %s ha %s del usuario %s', array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('relevant', 'Tocado ' . implode (' y ', $tocado)),
                            Feed::item('user', $user->name, $user->id)
                        )));
                        $log->doAdmin('user');
                        unset($log);

                        // mensaje de ok y volvemos a la lista de usuarios
                        Message::info('Datos actualizados');
                        return $this->redirect('/admin/users');

                    } else {
                        // si hay algun error volvemos a poner los datos en el formulario
                        $data = $_POST;
                        Message::error(implode('<br />', $errors));
                    }
                }

                // vista de editar usuario
                return array(
                        'folder' => 'users',
                        'file' => 'edit',
                        'user'=>$user,
                        'data'=>$data,
                        'nodes'=>$nodes
                );

                break;
            case 'manage':

                // si llega post: ejecutamos + mensaje + seguimos editando

                // operación y acción para el feed

                $sql = self::$manageSubAct[$subaction]['sql'];
                $log_action = self::$manageSubAct[$subaction]['log'];

                if (!empty($sql)) {

                    $user = Model\User::getMini($id);

                    if (Model\User::query($sql, array(':user'=>$id))) {

                        // mensaje de ok y volvemos a la gestion del usuario
//                            Message::info('Ha <strong>' . $log_action . '</strong> al usuario <strong>'.$user->name.'</strong> CORRECTAMENTE');
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

                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($user->id, 'user');
                    $log->populate('Operación sobre usuario (admin)', '/admin/users',
                        \vsprintf($log_text, array(
                            Feed::item('user', Session::getUser()->name, Session::getUserId()),
                            Feed::item('relevant', $log_action),
                            Feed::item('user', $user->name, $user->id)
                    )));
                    $log->doAdmin('user');
                    unset($log);

                    return $this->redirect('/admin/users/manage/'.$id);
                }

                $user = Model\User::get($id);

                $viewData = array(
                        'folder' => 'users',
                        'file' => 'manage',
                        'user'=>$user,
                        'nodes'=>$nodes
                    );

                $viewData['roles'] = Model\User::getRolesList();
                $viewData['langs'] = Lang::listAll('name', false);
                // quitamos el español
                unset($viewData['langs']['es']);

                // vista de gestión de usuario
                return $viewData;


                break;

            // aplicar idiomas
            case 'translang':

                if (!$this->hasPost('user')) {
                    Message::error('Hemos perdido de vista al usuario');
                    return $this->redirect('/admin/users');
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
            case 'impersonate':

                $user = Model\User::get($id);

                // vista de acceso a suplantación de usuario
                return array(
                        'folder' => 'users',
                        'file'   => 'impersonate',
                        'user'   => $user,
                        'nodes'=>$nodes
                );

                break;
            case 'move':
                $user = Model\User::get($id);

                if ($this->isPost()) {
                    $values = array(':id' => $id, ':node' => $this->getPost('node'));
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
                        $log->populate('User cambiado de nodo (admin)', '/admin/users',
                            \vsprintf($log_text, array(
                                Feed::item('user', Session::getUser()->name, Session::getUserId()),
                                Feed::item('user', $user->name, $user->id),
                                Feed::item('user', $nodes[$this->getPost('node')])
                        )));
                        Message::error($log->html);
                        $log->doAdmin('user');
                        unset($log);

                        return $this->redirect('/admin/users');

                    } catch(\PDOException $e) {
                        Message::error("Ha fallado! " . $e->getMessage());
                    }
                }

                // vista de acceso a suplantación de usuario
                return array(
                        'folder' => 'users',
                        'file'   => 'move',
                        'user'   => $user,
                        'nodes' => $nodes
                );

                break;

            case 'list':
            default:
                if (!empty($filters['filtered'])) {
                    $users = Model\User::getAll($filters, $admin_subnode);
                } else {
                    $users = array();
                }

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
                        'folder' => 'users',
                        'file' => 'list',
                        'users'=>$users,
                        'filters' => $filters,
                        'status' => $status,
                        'interests' => $interests,
                        'roles' => $roles,
                        'types' => $types,
                        'nodes' => $nodes,
                        'projects' => $projects,
                        'orders' => $orders
                );
            break;
        }

    }

}
