<?php

namespace Goteo\Controller {

    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;
    use Goteo\Application\Exception\ControllerAccessDeniedException;
    use Goteo\Application\Exception\ControllerException;
    use Goteo\Core\ACL,
        Goteo\Core\Redirection,
        Goteo\Model,
        Goteo\Library\Feed,
        Goteo\Library\Page,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Application\View,
        Goteo\Application\Message,
        Goteo\Application\Session,
        Goteo\Application\Config,
        Goteo\Library\Newsletter,
        Goteo\Library\Worth;

    class AdminController extends \Goteo\Core\Controller {

        // Array de usuarios con permisos especiales
        static public $supervisors = array(
            'contratos' => array(
                // paneles de admin permitidos
                'commons'
            )
        );
        // Array de los gestores que existen
        static public $options = array(
            'accounts' => array(
                'label' => 'Gestión de aportes',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'details' => array('label' => 'Detalles del aporte', 'item' => true),
                    'update' => array('label' => 'Cambiando el estado al aporte', 'item' => true),
                    'add' => array('label' => 'Aporte manual', 'item' => false),
                    'move' => array('label' => 'Reubicando el aporte', 'item' => true),
                    'execute' => array('label' => 'Ejecución del cargo', 'item' => true),
                    'cancel' => array('label' => 'Cancelando aporte', 'item' => true),
                    'report' => array('label' => 'Informe de proyecto', 'item' => true),
                    'viewer' => array('label' => 'Viendo logs', 'item' => false)
                ),
                'filters' => array('id' => '', 'methods' => '', 'investStatus' => 'all', 'projects' => '', 'name' => '', 'calls' => '', 'review' => '', 'types' => '', 'date_from' => '', 'date_until' => '', 'issue' => 'all', 'procStatus' => 'all', 'amount' => '', 'maxamount' => '')
            ),
            'banners' => array(
                'label' => 'Banners',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Banner', 'item' => false),
                    'edit' => array('label' => 'Editando Banner', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Banner', 'item' => true)
                )
            ),
			'bazar' => array(
                'label' => 'Gestión del Catálogo',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Elemento', 'item' => false),
                    'edit' => array('label' => 'Editando Elemento', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Elemento', 'item' => true)
                )
            ),
            'blog' => array(
                'label' => 'Blog',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Entrada', 'item' => false),
                    'edit' => array('label' => 'Editando Entrada', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Entrada', 'item' => true),
                    'reorder' => array('label' => 'Ordenando las entradas en Portada', 'item' => false),
                    'footer' => array('label' => 'Ordenando las entradas en el Footer', 'item' => false)
                ),
                'filters' => array('show' => 'owned', 'blog' => '')
            ),
            'calls' => array(
                'label' => 'Gestión de convocatorias',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva convocatoria', 'item' => false),
                    'projects' => array('label' => 'Gestionando proyectos de la convocatoria', 'item' => true),
                    'admins' => array('label' => 'Asignando administradores de la convocatoria', 'item' => true),
                    'posts' => array('label' => 'Entradas de blog en la convocatoria', 'item' => true),
                    'conf' => array('label' => 'Configurando la convocatoria', 'item' => true),
                    'dropconf' => array('label' => 'Gestionando parte económica de la convocatoria', 'item' => true)
                ),
                'filters' => array('status' => '', 'category' => '', 'caller' => '', 'name' => '', 'admin' => '','order' => '')
            ),
            'campaigns' => array(
                'label' => 'Convocatorias destacadas',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva convocatoria destacada', 'item' => false)
                )
            ),
            'categories' => array(
                'label' => 'Categorías',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Categoría', 'item' => false),
                    'edit' => array('label' => 'Editando Categoría', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Categoría', 'item' => true),
                    'keywords' => array('label' => 'Palabras clave', 'item' => false)
                )
            ),
            'commons' => array(
                'label' => 'Retornos colectivos',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'view' => array('label' => 'Gestión de retornos', 'item' => true),
                    'info' => array('label' => 'Información de contacto', 'item' => true),
                    'add' => array('label' => 'Nuevo retorno', 'item' => false),
                    'edit' => array('label' => 'Editando retorno', 'item' => true)
                ),
                'filters' => array('project' => '', 'status' => '', 'icon' => '', 'projStatus'=>'')
            ),
            'criteria' => array(
                'label' => 'Criterios de revisión',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Criterio', 'item' => false),
                    'edit' => array('label' => 'Editando Criterio', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Criterio', 'item' => true)
                ),
                'filters' => array('section' => 'project')
            ),
            'faq' => array(
                'label' => 'FAQs',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Pregunta', 'item' => false),
                    'edit' => array('label' => 'Editando Pregunta', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Pregunta', 'item' => true)
                ),
                'filters' => array('section' => 'node')
            ),
            'home' => array(
                'label' => 'Elementos en portada',
                'actions' => array(
                    'list' => array('label' => 'Gestionando', 'item' => false)
                )
            ),
            'glossary' => array(
                'label' => 'Glosario',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Término', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Término', 'item' => true)
                )
            ),
            'icons' => array(
                'label' => 'Tipos de Retorno',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Tipo', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Tipo', 'item' => true)
                ),
                'filters' => array('group' => '')
            ),
            'info' => array(
                'label' => 'Ideas about',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Idea', 'item' => true),
                    'add' => array('label' => 'Creando Idea', 'item' => false),
                    'translate' => array('label' => 'Traduciendo Idea', 'item' => true)
                )
            ),
            'invests' => array(
                'label' => 'Aportes',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'details' => array('label' => 'Detalles del aporte', 'item' => true)
                ),
                'filters' => array('methods' => '', 'status' => 'all', 'investStatus' => 'all', 'projects' => '', 'name' => '', 'calls' => '', 'types' => '')
            ),
            'licenses' => array(
                'label' => 'Licencias',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Licencia', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Licencia', 'item' => true)
                ),
                'filters' => array('group' => '', 'icon' => '')
            ),
            'mailing' => array(
                'label' => 'Comunicaciones',
                'actions' => array(
                    'list' => array('label' => 'Seleccionando destinatarios', 'item' => false),
                    'edit' => array('label' => 'Escribiendo contenido', 'item' => false),
                    'send' => array('label' => 'Comunicación enviada', 'item' => false)
                ),
                'filters' => array('project' => '', 'type' => '', 'status' => '-1', 'method' => '', 'interest' => '', 'role' => '', 'name' => '', 'donant' => '', 'comlang' => ''
                )
            ),
            'news' => array(
                'label' => 'Micronoticias',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Micronoticia', 'item' => false),
                    'edit' => array('label' => 'Editando Micronoticia', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Micronoticia', 'item' => true)
                )
            ),
            'newsletter' => array(
                'label' => 'Boletín',
                'actions' => array(
                    'list' => array('label' => 'Estado del envío automático', 'item' => false),
                    'init' => array('label' => 'Iniciando un nuevo envío', 'item' => false),
                    'activate' => array('label' => 'Iniciando envío', 'item' => true),
                    'detail' => array('label' => 'Viendo destinatarios', 'item' => true)
                ),
                'filters' => array('show' => 'receivers')
            ),
            'node' => array(
                'label' => 'Datos del Canal',
                'actions' => array(
                    'list' => array('label' => 'Datos actuales', 'item' => false),
                    'edit' => array('label' => 'Editando', 'item' => false),
                    'admins' => array('label' => 'Viendo administradores', 'item' => false)
                )
            ),
            'nodes' => array(
                'label' => 'Gestión de Canales',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Canal', 'item' => false),
                    'edit' => array('label' => 'Gestionando Canal', 'item' => true),
                    'admins' => array('label' => 'Asignando administradores del Canal', 'item' => true)
                ),
                'filters' => array('status' => '', 'admin' => '', 'name' => '')
            ),
            'open_tags' => array(
                'label' => 'Agrupaciones',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Agrupación', 'item' => false),
                    'edit' => array('label' => 'Editando Agrupación', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Agrupación', 'item' => true)
                )
            ),
            'pages' => array(
                'label' => 'Páginas',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Página', 'item' => true),
                    'add' => array('label' => 'Nueva Página', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Página', 'item' => true)
                )
            ),
            'patron' => array(
                'label' => 'Padrinos',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo apadrinamiento', 'item' => false),
                    'edit' => array('label' => 'Editando Apadrinamiento', 'item' => true),
                    'view' => array('label' => 'Apadrinamientos', 'item' => true),
                    'reorder' => array('label' => 'Ordenando los padrinos en Portada', 'item' => false)
                )
            ),
            'projects' => array(
                'label' => 'Gestión de proyectos',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'dates' => array('label' => 'Fechas del proyecto', 'item' => true),
                    'accounts' => array('label' => 'Cuentas del proyecto', 'item' => true),
                    'images' => array('label' => 'Imágenes del proyecto', 'item' => true),
                    'move' => array('label' => 'Moviendo a otro Nodo el proyecto', 'item' => true),
                    'assign' => array('label' => 'Asignando a una Convocatoria el proyecto', 'item' => true),
                    'open_tags' => array('label' => 'Asignando una agrupación al proyecto', 'item' => true),
                    'report' => array('label' => 'Informe Financiero del proyecto', 'item' => true),
                    'rebase' => array('label' => 'Cambiando Id de proyecto', 'item' => true),
                    'consultants' => array('label' => 'Cambiando asesor del proyecto', 'item' => true),
                    'conf' => array('label' => 'Configuración de campaña del proyecto', 'item' => true)
                ),
                'filters' => array('status' => '-1', 'category' => '', 'proj_name' => '', 'name' => '', 'node' => '', 'called' => '', 'order' => '', 'consultant' => '','proj_id' =>'')
            ),
            'promote' => array(
                'label' => 'Proyectos destacados',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Destacado', 'item' => false),
                    'edit' => array('label' => 'Editando Destacado', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Destacado', 'item' => true)
                )
            ),
            'recent' => array(
                'label' => 'Actividad reciente',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false)
                )
            ),
            'reports' => array(
                'label' => 'Informes',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'paypal' => array('label' => 'Informe PayPal', 'item' => false),
                    'geoloc' => array('label' => 'Informe usuarios Localizados', 'item' => false),
                    'projects' => array('label' => 'Informe Impulsores', 'item' => true),
                    'calls' => array('label' => 'Informe Convocatorias', 'item' => true),
                    'donors' => array('label' => 'Informe Donantes', 'item' => false),
                    'top' => array('label' => 'Top Cofinanciadores', 'item' => false),
                    'currencies' => array('label' => 'Actuales ratios de conversión', 'item' => false)
                ),
                'filters' => array('report' => '', 'date_from' => '', 'date_until' => '', 'year' => '2014', 'status' => '', 'user' => '', 'top'=>'numproj', 'limit'=>25)
            ),
            'reviews' => array(
                'label' => 'Revisiones',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Iniciando briefing', 'item' => false),
                    'edit' => array('label' => 'Editando briefing', 'item' => true),
                    'report' => array('label' => 'Informe', 'item' => true)
                ),
                'filters' => array('project' => '', 'status' => 'open', 'checker' => '')
            ),
            'rewards' => array(
                'label' => 'Recompensas',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Gestionando recompensa', 'item' => true)
                ),
                'filters' => array('project' => '', 'name' => '', 'status' => '', 'friend' => '')
            ),
            'sended' => array(
                'label' => 'Historial envíos',
                'actions' => array(
                    'list' => array('label' => 'Emails enviados', 'item' => false)
                ),
                'filters' => array('user' => '', 'template' => '', 'node' => '', 'date_from' => '', 'date_until' => '')
            ),
            'sponsors' => array(
                'label' => 'Apoyos institucionales',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Patrocinador', 'item' => false),
                    'edit' => array('label' => 'Editando Patrocinador', 'item' => true)
                )
            ),
            'stories' => array(
                'label' => 'Historias exitosas',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Historia', 'item' => false),
                    'edit' => array('label' => 'Editando Historia', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Historia', 'item' => true),
                    'preview' => array('label' => 'Previsualizando Historia', 'item' => true)
                )
            ),
            'tags' => array(
                'label' => 'Tags de blog',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nuevo Tag', 'item' => false),
                    'edit' => array('label' => 'Editando Tag', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Tag', 'item' => true)
                )
            ),
            'tasks' => array(
                'label' => 'Tareas admin',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Nueva Tarea', 'item' => false),
                    'edit' => array('label' => 'Editando Tarea', 'item' => true)
                ),
                'filters' => array('done' => '', 'user' => '', 'node' => '')
            ),
            'templates' => array(
                'label' => 'Plantillas de email',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Plantilla', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Plantilla', 'item' => true)
                ),
                'filters' => array('id'=>'', 'group' => '', 'name' => '')
            ),
            'texts' => array(
                'label' => 'Textos interficie',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Original', 'item' => true),
                    'translate' => array('label' => 'Traduciendo Texto', 'item' => true)
                ),
                'filters' => array('group' => '', 'text' => '')
            ),
            'translates' => array(
                'label' => 'Traducciones de proyectos',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Habilitando traducción', 'item' => false),
                    'edit' => array('label' => 'Asignando traducción', 'item' => true)
                ),
                'filters' => array('owner' => '', 'translator' => '')
            ),
            'transcalls' => array(
                'label' => 'Traducciones de convocatorias',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Habilitando traducción', 'item' => false),
                    'edit' => array('label' => 'Asignando traducción', 'item' => true)
                ),
                'filters' => array('owner' => '', 'translator' => '')
            ),
            'transnodes' => array(
                'label' => 'Traducciones de nodos',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Habilitando traducción', 'item' => false),
                    'edit' => array('label' => 'Asignando traducción', 'item' => true)
                ),
                'filters' => array('admin' => '', 'translator' => '')
            ),
            'users' => array(
                'label' => 'Gestión de usuarios',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'add' => array('label' => 'Creando Usuario', 'item' => true),
                    'edit' => array('label' => 'Editando Usuario', 'item' => true),
                    'manage' => array('label' => 'Gestionando Usuario', 'item' => true),
                    'impersonate' => array('label' => 'Suplantando al Usuario', 'item' => true),
                    'move' => array('label' => 'Moviendo a otro Nodo el usuario ', 'item' => true)
                ),
                'filters' => array('interest' => '', 'role' => '', 'node' => '', 'id' => '', 'name' => '', 'order' => '', 'project' => '', 'type' => '')
            ),
            'wordcount' => array(
                'label' => 'Conteo de palabras',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false)
                )
            ),
            'worth' => array(
                'label' => 'Niveles de meritocracia',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'edit' => array('label' => 'Editando Nivel', 'item' => true)
                )
            )
        );

        private static $subcontrollers = array();

        public static function addSubController($classname) {
            self::$subcontrollers[] = $classname;
        }

        /**
         * Security method
         * Gets the current user
         * Gets the menu
         * Sets the current node to admin from the user or the get Request
         * @param Model\User $user    [description]
         * @param Request    $request [description]
         */
        private static function checkCurrentUser(Request $request, $option = null, $action = null, $id = null) {
            //refresh permission status
            Model\User::flush();
            if ( ! $user = Session::getUser() ) {
                throw new ControllerAccessDeniedException("Not logged!");
            }


            // get working node
            $nodes = array();
            foreach($user->getAdminNodes() as $node_id => $node) {
                $nodes[$node_id] = $node->name;
            }
            $node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
            //if need to change the current node
            if($request->query->has('node') && array_key_exists($request->query->get('node'), $nodes)) {
                $node = $request->query->get('node');
                Session::store('admin_node', $node);
            }

            $menu = array();
            $breadcrumb = array(['Admin', '/admin']);
            // Build menu from subcontrollers for the current user/node
            foreach(self::$subcontrollers as $class) {
                if($class::isAllowed($user, $node)) {
                    $menu[$class::getId()] = $class::getLabel();
                    if($option === $class::getId()) {
                        // add option
                        $breadcrumb[] = [$class::getLabel(), $class::getUrl()];
                        // add action
                        if($action)
                            $breadcrumb[] = [
                                    $class::getLabel($action) . ($id ? " [$id]" : ''),
                                    ($action === 'list' || $id) ? '' : $class::getUrl($action, $id)
                                ];
                    }
                }
            }

            View::getEngine()->useContext('admin/', [
                    'option' => $option,
                    'admin_menu' => $menu,
                    'admin_node' => $node,
                    'admin_nodes' => $nodes,
                    'breadcrumb' => $breadcrumb
                ]);

            // If menu is not allowed, throw exception
            if($option && ! array_key_exists($option, $menu) ) {
                $zone = $menu[$option];
                if($zone) $msg = 'Access denied to <strong>' . $zone . '</strong>';
                else      $msg = 'Access denied!';
                Message::error($msg);
                throw new ControllerAccessDeniedException($msg);
            }

            return $user;
        }

        /** Default index action */
        public function indexAction(Request $request) {
            $ret = array();
            try {
               $user = self::checkCurrentUser($request);
            } catch(ControllerAccessDeniedException $e) {
                // Instead of the default denied page, redirect to login
                Message::error($e->getMessage());
                return new RedirectResponse('/user/login');
            }

            //feed by default for someones
            if($nodes = $user->getAdminNodes()) {
                //TODO: allow Feed to handle multiple nodes
                $ret['feed'] = \Goteo\Library\Feed::getAll('all', 'admin', 50, Session::get('admin_node'));
            }
            //default admin dashboard (nothing!)
            return new Response(View::render('admin/default', $ret));

        }

        // preparado para index unificado
        public function optionAction($option, $action = 'list', $id = null, $subaction = null, Request $request) {
            $ret = array();
            $SubC = 'Goteo\Controller\Admin\\' . \strtoCamelCase($option) . 'SubController';

            try {
                $user = self::checkCurrentUser($request, $option, $action, $id);
                if( ! class_exists($SubC) ) {
                    return new Response(View::render('admin/denied', ['msg' => "Class [$SubC] not found"]), Response::HTTP_BAD_REQUEST);
                }
                $node = Session::exists('admin_node') ? Session::get('admin_node') : Config::get('node');
                $controller = new $SubC($node, $request);
                $method = $action . 'Action';
                if( ! method_exists($controller, $method) ) {
                    return new Response(View::render('admin/denied', ['msg' => "Method [$method()] not found for class [$SubC]"]), Response::HTTP_BAD_REQUEST);
                }
                $controller->setFilters(self::setFilters($option));
                $ret = $controller->$method($id, $subaction);

            } catch(ControllerAccessDeniedException $e) {
                // Instead of the default denied page, redirect to login
                Message::error($e->getMessage());
                return new RedirectResponse('/admin');
            }

            //Return the response if the subcontroller is a handy guy
            if($ret instanceOf Response) {
                return $ret;
            }

            // Old view compatibility
            // They return a file to be rendered along with vars
            $old_path = $ret['old_view_path'];
            if(!$old_path && $ret['folder'] && $ret['file']) {
                $old_path = 'admin/' . ($ret['folder'] === 'base' ? '' : $ret['folder'] . '/') . $ret['file'].'.html.php';
            }
            if ($old_path) {
                return new Response(View::render('admin/simple', [
                    'content' => \Goteo\Core\View::get($old_path, $ret)
                    ]));
            }

            // If the subcontroller just specifies a template to render let's do it
            if ($ret['template']) {
                  return new Response(View::render($ret['template'], $ret));
            }

            //default admin dashboard (nothing!)
            return new Response(View::render('admin/default', $ret));

        }

        /*
         * Si no tenemos filtros para este gestor los cogemos de la sesion
         */

        private static function setFilters($option) {

            // array de fltros para el sub controlador
            $filters = array();
            if(!is_array(self::$options[$option]['filters'])) return $filters;

            if (isset($_GET['reset']) && $_GET['reset'] == 'filters') {
                unset($_SESSION['admin_filters'][$option]);
                unset($_SESSION['admin_filters']['main']);
                foreach (self::$options[$option]['filters'] as $field => $default) {
                    $filters[$field] = $default;
                }
                return $filters;
            }

            // si hay algun filtro
            $filtered = false;

            // filtros de este gestor:
            // para cada uno tenemos el nombre del campo y el valor por defecto
            foreach (self::$options[$option]['filters'] as $field => $default) {
                if (isset($_GET[$field])) {
                    // si lo tenemos en el get, aplicamos ese a la sesión y al array
                    $filters[$field] = (string) $_GET[$field];
                    $_SESSION['admin_filters'][$option][$field] = (string) $_GET[$field];
                    if (($option == 'reports' && $field == 'user')
                            || ($option == 'projects' && $field == 'user')
                            || ($option == 'users' && $field == 'name')
                            || ($option == 'accounts' && $field == 'name')
                            || ($option == 'rewards' && $field == 'name')) {

                        $_SESSION['admin_filters']['main']['user_name'] = (string) $_GET[$field];
                    }
                    $filtered = true;
                } elseif (!empty($_SESSION['admin_filters'][$option][$field])) {
                    // si no lo tenemos en el get, cogemos de la sesion pero no lo pisamos
                    $filters[$field] = $_SESSION['admin_filters'][$option][$field];
                    $filtered = true;
                } else {
                    // a ver si tenemos un filtro equivalente
                    switch ($option) {
                        case 'projects':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'users':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'accounts':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'rewards':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                    }

                    // si no tenemos en sesion, ponemos el valor por defecto
                    if (empty($filters[$field])) {
                        $filters[$field] = $default;
                    }
                }
            }

            if ($filtered) {
                $filters['filtered'] = 'yes';
            }

            return $filters;
        }

        /*
         * Diferentes menus para diferentes perfiles
         * El menu del panel admin dependerá del rol del usuario que accede
         * Superadmin = todo
         * Admin = contenidos de Nodo
         * Supervisor = menus especiales
         */
        private static function getMenu($role, $node, $user = null) {

            $options = self::$options;
            $menu = array();
            switch ($role) {
                case 'supervisor':
                    $menu = array(
                        'contents' => array(
                            'label' => 'Gestores',
                            'options' => array()
                        )
                    );

                    foreach (self::$supervisors[$user] as $opt) {
                        $menu['contents']['options'][$opt] = $options[$opt];
                    }

                    break;
                case 'admin':
                    $menu = array(
                        'contents' => array(
                            'label' => 'Contenidos',
                            'options' => array(
                                'node' => $options['node'], // la gestion de datos del nodo
                                'pages' => $options['pages'], // páginas institucionales del nodo
                                'blog' => $options['blog'], // entradas del blog
                                'banners' => $options['banners']    // banners del nodo
                            )
                        ),
                        'projects' => array(
                            'label' => 'Proyectos',
                            'options' => array(
                                'projects' => $options['projects'], // proyectos del nodo
                                'reviews' => $options['reviews'], // revisiones de proyectos del nodo
                                'translates' => $options['translates'], // traducciones de proyectos del nodo
                                'invests' => $options['invests'],
                                'patron' => $options['patron'],
                                'commons' => $options['commons'],
                                'calls' => $options['calls']
                            )
                        ),
                        'users' => array(
                            'label' => 'Usuarios',
                            'options' => array(
                                'users' => $options['users'], // usuarios asociados al nodo
                                'mailing' => $options['mailing'], // comunicaciones del nodoc on sus usuarios / promotores
                                'sended' => $options['sended'], // historial de envios realizados por el nodo
                                'tasks' => $options['tasks']  // gestión de tareas
                            )
                        ),
                        'home' => array(
                            'label' => 'Portada',
                            'options' => array(
                                'home' => $options['home'], // elementos en portada
                                'promote' => $options['promote'], // seleccion de proyectos destacados
                                'campaigns' => $options['campaigns'], // convocatorias en portada
                                'blog' => $options['blog'], // entradas de blog (en la gestion de blog)
                                'sponsors' => $options['sponsors'], // patrocinadores del nodo
                                'stories' => $options['stories'],    // historias exitosas
                                'recent' => $options['recent'],
                                'news' => $options['news'] // Banner de prensa
                            )
                        )
                    );

                    // para admines de central
                    if ($node === Config::get('node')) {
                        unset($menu['contents']['options']['node']);
                        unset($menu['projects']['options']['invests']);
                        $menu['projects']['options']['accounts'] = $options['accounts']; // gestion completa de aportes
                        $menu['projects']['options']['rewards'] = $options['rewards']; // gestion de recompensas de aportes
                        $menu['projects']['options']['reports'] = $options['reports']; // informes
                        $menu['contents']['options']['texts'] = $options['texts']; // gestión de textos
                        $menu['contents']['options']['faq'] = $options['faq']; // gestión de faqs
                        $menu['contents']['options']['templates'] = $options['templates']; // gestión de plantillas
                        $menu['projects']['options']['transcalls'] = $options['transcalls']; // traducción de convocatorias
                        $menu['projects']['options']['commons'] = $options['commons']; // gestion de retornos colectivos
                        $menu['projects']['options']['bazar'] = $options['bazar']; // gestion de retornos colectivos
                        $menu['contents']['options']['open_tags'] = $options['open_tags']; // gestión de agrupaciones
                    }
                    break;
                case 'superadmin':
                    $menu = array(
                        'contents' => array(
                            'label' => 'Textos y Traducciones',
                            'options' => array(
                                'blog' => $options['blog'],
                                'texts' => $options['texts'],
                                'faq' => $options['faq'],
                                'pages' => $options['pages'],
                                'categories' => $options['categories'],
                                'licenses' => $options['licenses'],
                                'icons' => $options['icons'],
                                'open_tags' => $options['open_tags'],
                                'tags' => $options['tags'],
                                'criteria' => $options['criteria'],
                                'templates' => $options['templates'],
                                'glossary' => $options['glossary'],
                                'info' => $options['info'],
                                'wordcount' => $options['wordcount']
                            )
                        ),
                        'projects' => array(
                            'label' => 'Proyectos',
                            'options' => array(
                                'projects' => $options['projects'],
                                'accounts' => $options['accounts'],
                                'patron' => $options['patron'],
                                'reviews' => $options['reviews'],
                                'translates' => $options['translates'],
                                'rewards' => $options['rewards'],
                                'commons' => $options['commons'],
                                'bazar' => $options['bazar']
                            )
                        ),
                        'users' => array(
                            'label' => 'Usuarios',
                            'options' => array(
                                'users' => $options['users'],
                                'worth' => $options['worth'],
                                'mailing' => $options['mailing'],
                                'sended' => $options['sended'],
                                'tasks' => $options['tasks']
                            )
                        ),
                        'home' => array(
                            'label' => 'Portada',
                            'options' => array(
                                'news' => $options['news'],
                                'banners' => $options['banners'],
                                'stories' => $options['stories'],
                                'blog' => $options['blog'],
                                'promote' => $options['promote'],
                                'footer' => $options['footer'],
                                'recent' => $options['recent'],
                                'home' => $options['home']
                            )
                        ),
                        'sponsors' => array(
                            'label' => 'Servicios',
                            'options' => array(
                                'reports' => $options['reports'],
                                'newsletter' => $options['newsletter'],
                                'sponsors' => $options['sponsors'],
                                'calls' => $options['calls'],
                                'transcalls' => $options['transcalls'],
                                'nodes' => $options['nodes'],
                                'transnodes' => $options['transnodes'],
                                'currencies' => $options['currencies']
                            )
                        )
                    );
                    break;
            }

            // para administradores de nodo
            // TODO: barcelona hardcoded!
            if ($node !== Config::get('node') && $node !== 'barcelona') {

                unset($menu['contents']['options']['pages']);
                unset($menu['contents']['options']['blog']);
                unset($menu['contents']['options']['banners']);
                unset($menu['projects']['options']['invests']);
                unset($menu['projects']['options']['patron']);
                unset($menu['projects']['options']['commons']);
                unset($menu['projects']['options']['calls']);

                unset($menu['users']);

                unset($menu['home']['options']['news']);
                unset($menu['home']['options']['blog']);
                unset($menu['home']['options']['recent']);
                unset($menu['home']['options']['sponsors']);
                unset($menu['home']['options']['stories']);


            }

            return $menu;
        }

    }

}
