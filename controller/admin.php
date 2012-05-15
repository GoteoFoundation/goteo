<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
		Goteo\Library\Feed,
		Goteo\Library\Lang,
        Goteo\Library\Page,
        Goteo\Library\Mail,
        Goteo\Library\Template,
        Goteo\Library\Message,
        Goteo\Library\Newsletter,
        Goteo\Library\Worth;

	class Admin extends \Goteo\Core\Controller {

            // Array de usuarios con permisos especiales
            static public $supervisors = array(
                'diegobus' => array(
                        'base',
                        'blog',
                        'texts',
                        'faq',
                        'pages',
                        'licenses',
                        'icons',
                        'tags',
                        'criteria',
                        'templates',
                        'glossary',
                        'info',
                        'mailing' // para testeo newsletter
                    ),
                'merxxx' => array(
                        'users',
                        'accounts',
                        'recent'
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
                            'add'  => array('label' => 'Aporte manual', 'item' => false),
                            'move'  => array('label' => 'Reubicando el aporte', 'item' => true),
                            'execute' => array('label' => 'Ejecución del cargo', 'item' => true),
                            'cancel' => array('label' => 'Cancelando aporte', 'item' => true),
                            'report' => array('label' => 'Informe de proyecto', 'item' => true),
                            'viewer' => array('label' => 'Viendo logs', 'item' => false)
                        ),
                        'filters' => array('id'=>'', 'methods'=>'', 'investStatus'=>'all', 'projects'=>'', 'name'=>'', 'calls'=>'', 'review'=>'', 'types'=>'', 'date_from'=>'', 'date_until'=>'', 'issue'=>'all')
                    ),
                    'banners' => array(
                        'label' => 'Banners',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Banner', 'item' => false),
                            'edit' => array('label' => 'Editando Banner', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Banner', 'item' => true)
                        )
                    ),
                    'blog' => array(
                        'label' => 'Blog',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Entrada', 'item' => false),
                            'edit' => array('label' => 'Editando Entrada', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Entrada', 'item' => true),
                            'reorder' => array('label' => 'Ordenando las entradas en Portada', 'item' => false)
                        ),
                        'filters' => array('show'=>'all')
                    ),
                    'calls' => array(
                        'label' => 'Gestión de convocatorias',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva convocatoria', 'item' => false),
                            'projects' => array('label' => 'Gestionando proyectos de la convocatoria', 'item' => true)
                        ),
                        'filters' => array('status'=>'', 'category'=>'', 'owner'=>'', 'name'=>'', 'order'=>'')
                    ),
                    'campaigns' => array(
                        'label' => 'Campañas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva campaña en portada', 'item' => false)
                        )
                    ),
                    'categories' => array(
                        'label' => 'Categorías',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Categoría', 'item' => false),
                            'edit' => array('label' => 'Editando Categoría', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Categoría', 'item' => true)
                        )
                    ),
                    'commons' => array(
                        'label' => 'Retornos colectivos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false)
                        ),
                        'filters' => array('status'=>'', 'icon'=>'')
                    ),
                    'criteria' => array(
                        'label' => 'Criterios de revisión',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Criterio', 'item' => false),
                            'edit' => array('label' => 'Editando Criterio', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Criterio', 'item' => true)
                        ),
                        'filters' => array('section'=>'project')
                    ),
                    'faq' => array(
                        'label' => 'FAQs',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Pregunta', 'item' => false),
                            'edit' => array('label' => 'Editando Pregunta', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Pregunta', 'item' => true)
                        ),
                        'filters' => array('section'=>'node')
                    ),
                    'recent' => array(
                        'label' => 'Actividad reciente',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false)
                        )
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
                        'filters' => array('group'=>'')
                    ),
                    'info' => array(
                        'label' => 'Ideas about',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Idea', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Idea', 'item' => true)
                        )
                    ),
                    'invests' => array(
                        'label' => 'Aportes',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'details' => array('label' => 'Detalles del aporte', 'item' => true)
                        ),
                        'filters' => array('methods'=>'', 'status'=>'all', 'investStatus'=>'all', 'projects'=>'', 'users'=>'', 'calls'=>'', 'types'=>'')
                    ),
                    'licenses' => array(
                        'label' => 'Licencias',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Licencia', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Licencia', 'item' => true)
                        ),
                        'filters' => array('group'=>'', 'icon'=>'')
                    ),
                    'mailing' => array(
                        'label' => 'Comunicaciones',
                        'actions' => array(
                            'list' => array('label' => 'Seleccionando destinatarios', 'item' => false),
                            'edit' => array('label' => 'Escribiendo contenido', 'item' => false),
                            'send' => array('label' => 'Comunicación enviada', 'item' => false)
                        ),
                        'filters' => array('project'=>'', 'type'=>'', 'status'=>'-1', 'method'=>'', 'interest'=>'', 'role'=>'', 'name'=>'', 'workshopper'=>'',
                        )
                    ),
                    'news' => array(
                        'label' => 'Micronoticias',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Micronoticia', 'item' => false),
                            'edit' => array('label' => 'Editando Micronoticia', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Micronoticia', 'item' => true)
                        )
                    ),
                    'newsletter' => array(
                        'label' => 'Boletín',
                        'actions' => array(
                            'list' => array('label' => 'Estado del envío automático', 'item' => false),
                            'init' => array('label' => 'Iniciando un nuevo boletín', 'item' => false),
                            'init' => array('label' => 'Viendo listado completo', 'item' => true)
                        )
                    ),
                    'node' => array(
                        'label' => 'Datos del Nodo',
                        'actions' => array(
                            'list' => array('label' => 'Datos actuales', 'item' => false),
                            'edit' => array('label' => 'Editando', 'item' => false),
                            'admins' => array('label' => 'Viendo administradores', 'item' => false)
                        )
                    ),
                    'nodes' => array(
                        'label' => 'Gestión de Nodos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Nodo', 'item' => false),
                            'edit' => array('label' => 'Gestionando Nodo', 'item' => true),
                            'admins' => array('label' => 'Asignando administradores del Nodo', 'item' => true)
                        ),
                        'filters' => array('status'=>'', 'admin'=>'', 'name'=>'')
                    ),
                    'pages' => array(
                        'label' => 'Páginas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Página', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Página', 'item' => true)
                        )
                    ),
                    'patron' => array(
                        'label' => 'Padrinos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nueva Recomendación', 'item' => false),
                            'edit' => array('label' => 'Editando Recomendacion', 'item' => true)
                        )
                    ),
                /*
                 * Para poner entradas en portada y reordenar, se usa la misma gestion de blog
                 *
                    'posts' => array(
                        'label' => 'Entradas de blog en Portada',
                        'actions' => array(
                            'list' => array('label' => 'Ordenando', 'item' => false),
                            'add'  => array('label' => 'Colocando Entrada en la portada', 'item' => false)
                        )
                    ),
                 *
                 */
                    'projects' => array(
                        'label' => 'Gestión de proyectos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'dates' => array('label' => 'Fechas del proyecto', 'item' => true),
                            'accounts' => array('label' => 'Cuentas del proyecto', 'item' => true),
                            'move' => array('label' => 'Moviendo a otro Nodo el proyecto', 'item' => true),
                            'report' => array('label' => 'Informe Financiero del proyecto', 'item' => true)
                        ),
                        'filters' => array('status'=>'-1', 'category'=>'', 'proj_name'=>'', 'name'=>'', 'node'=>'', 'order'=>'')
                    ),
                    'promote' => array(
                        'label' => 'Proyectos destacados',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Destacado', 'item' => false),
                            'edit' => array('label' => 'Editando Destacado', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Destacado', 'item' => true)
                        )
                    ),
                    'reviews' => array(
                        'label' => 'Revisiones',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Iniciando briefing', 'item' => false),
                            'edit' => array('label' => 'Editando briefing', 'item' => true),
                            'report' => array('label' => 'Informe', 'item' => true)
                        ),
                        'filters' => array('status'=>'', 'checker'=>'')
                    ),
                    'rewards' => array(
                        'label' => 'Recompensas',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Gestionando recompensa', 'item' => true)
                        ),
                        'filters' => array('projects'=>'', 'name'=>'')
                    ),
                    'sended' => array(
                        'label' => 'Historial envíos',
                        'actions' => array(
                            'list' => array('label' => 'Emails enviados', 'item' => false)
                        ),
                        'filters' => array('user'=>'', 'template'=>'', 'node'=>\GOTEO_NODE)
                    ),
                    'sponsors' => array(
                        'label' => 'Apoyos institucionales',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Patrocinador', 'item' => false),
                            'edit' => array('label' => 'Editando Patrocinador', 'item' => true)
                        )
                    ),
                    'tags' => array(
                        'label' => 'Tags de blog',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Nuevo Tag', 'item' => false),
                            'edit' => array('label' => 'Editando Tag', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Tag', 'item' => true)
                        )
                    ),
                    'tasks' => array(
                        'label' => 'Tareas admin',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Tarea', 'item' => true)
                        ),
                        'filters' => array('done'=>'', 'user'=>'', 'node'=>'')
                    ),
                    'templates' => array(
                        'label' => 'Plantillas de email',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Plantilla', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Plantilla', 'item' => true)
                        )
                    ),
                    'texts' => array(
                        'label' => 'Textos interficie',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'edit' => array('label' => 'Editando Original', 'item' => true),
                            'translate' => array('label' => 'Traduciendo Texto', 'item' => true)
                        ),
                        'filters' => array('idfilter'=>'', 'group'=>'', 'text'=>'')
                    ),
                    'translates' => array(
                        'label' => 'Traducciones de proyectos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Habilitando traducción', 'item' => false),
                            'edit' => array('label' => 'Asignando traducción', 'item' => true)
                        ),
                        'filters' => array('owner'=>'', 'translator'=>'')
                    ),
                    'transcalls' => array(
                        'label' => 'Traducciones de convocatorias',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Habilitando traducción', 'item' => false),
                            'edit' => array('label' => 'Asignando traducción', 'item' => true)
                        ),
                        'filters' => array('owner'=>'', 'translator'=>'')
                    ),
                    'transnodes' => array(
                        'label' => 'Traducciones de nodos',
                        'actions' => array(
                            'list' => array('label' => 'Listando', 'item' => false),
                            'add'  => array('label' => 'Habilitando traducción', 'item' => false),
                            'edit' => array('label' => 'Asignando traducción', 'item' => true)
                        ),
                        'filters' => array('admin'=>'', 'translator'=>'')
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
                        'filters' => array('status'=>'active', 'interest'=>'', 'role'=>'', 'node'=>'', 'id'=>'', 'name'=>'', 'order'=>'', 'project'=>'')
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

        public function index () {
            $BC = self::menu(array('option'=>'index', 'action'=>null, 'id' => null));
            define('ADMIN_BCPATH', $BC);
            $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
            $tasks = Model\Task::getAll(array(), $node, true);
            return new View('view/admin/index.html.php', array('tasks'=>$tasks));
        }

        public function done ($id) {
            $errors = array();
            if (!empty($id) && isset($_SESSION['user']->id)) {
                $task = Model\Task::get($id);
                if ($task->setDone($errors)) {
                    Message::Info('La tarea se ha marcado como realizada');
                } else {
                    Message::Error(implode('<br />', $errors));
                }
            } else {
                Message::Error('Faltan datos');
            }
            throw new Redirection('/admin');
        }

        /*
         * Info de Actividad reciente para los administradores
         */
        public function recent ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Recent::process($action, $id);
        }

        /*
         * Gestión de páginas institucionales
         */
		public function pages ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Pages::process($action, $id);
		}

        /*
         * Gestion de textos dinámicos
         */
		public function texts ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            // no cache para textos
            define('GOTEO_ADMIN_NOCACHE', true);
            return Admin\Texts::process($action, $id, self::setFilters(__FUNCTION__));
		}

        /*
         * Gestión de plantillas para emails automáticos
         */
		public function templates ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Templates::process($action, $id);
		}

        /*
         *  Lista de proyectos
         */
        public function projects($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Projects::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  Revision de proyectos
         */
        public function reviews($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Reviews::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  Traducciones de proyectos
         */
        public function translates($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Translates::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  Traducciones de convocatorias
         */
        public function transcalls($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Transcalls::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  Traducciones de nodos
         */
        public function transnodes($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Transnodes::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * proyectos destacados
         */
        public function promote($action = 'list', $id = null, $flag = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Promote::process($action, $id, $flag);
        }

        /*
         * proyectos recomendados por padrinos
         */
        public function patron($action = 'list', $id = null, $flag = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Patron::process($action, $id, $flag);
        }

        /*
         * Banners
         */
        public function banners($action = 'list', $id = null, $flag = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Banners::process($action, $id, $flag);
        }

        /*
         * preguntas frecuentes
         */
        public function faq($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Faq::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * criterios de puntuación Goteo
         */
        public function criteria($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Criteria::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * Tipos de Retorno/Recompensa (iconos)
         */
        public function icons($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Icons::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * Licencias
         */
        public function licenses($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Licenses::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * posts para portada
         */
        public function posts($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Posts::process($action, $id);
        }

        /*
         * posts para pie
         */
        public function footer($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Footer::process($action, $id);
        }

        /*
         *  Gestión de categorias de proyectos
         */
        public function categories($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Categories::process($action, $id);
        }

        /*
         *  Gestión de tags de blog
         */
        public function tags($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Tags::process($action, $id);
        }

        /*
         *  Gestión de tareas pendientes de administracion
         */
        public function tasks($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Tasks::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  administración de usuarios para superadmin
         */
        public function users($action = 'list', $id = null, $subaction = '') {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Users::process($action, $id, $subaction, self::setFilters(__FUNCTION__));
        }

        /*
         *  Gestión de aportes a proyectos
         */
        public function invests($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Invests::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  Gestión transacciones (tpv/paypal)
         * 
         */
        public function accounts($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Accounts::process($action, $id, self::setFilters(__FUNCTION__));
        }


        /*
         * Gestión de retornos colectivos, para marcar un retorno colectivo como cumplido
         */
        public function commons($action = 'list', $id = null, $filters = array()) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Commons::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * Gestión de recompensas y dirección de los aportes
         */
        public function rewards($action = 'list', $id = null, $filters = array()) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Rewards::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * Gestión de entradas de blog
         */
        public function blog ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Blog::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * Gestión de términos del Glosario
         */
        public function glossary ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Glossary::process($action, $id);
        }

        /*
         * Gestión de entradas de info
         */
        public function info ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Info::process($action, $id);
        }


        /*
         *  Gestión de noticias
         */
        public function news($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\News::process($action, $id);
        }

        /*
         * Gestor de envio automático de newsletter
         */
        public function newsletter ($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Newsletter::process($action, $id);
        }


        /*
         *  Gestión de patrocinadores
         */
        public function sponsors($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Sponsors::process($action, $id);
        }

        /*
         *  Lista de convocatorias
         */
        public function calls($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Calls::process($action, $id);
        }

        /*
         *  Convocatorias en portada
         */
        public function campaigns($action = 'list', $id = null, $flag = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Campaigns::process($action, $id, $flag);
        }

        /*
         *  Gestión de nodos
         */
        public function nodes($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Nodes::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  Gestión de datos del nodo
         */
        public function node($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Node::process($action, $id);
        }

        /*
         * Comunicaciones con los usuarios mediante mailing
         */
        public function mailing($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Mailing::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         *  historial de emails enviados
         */
        public function sended($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Sended::process($action, $id, self::setFilters(__FUNCTION__));
        }

        /*
         * Niveles de meritocracia
         */
        public function worth($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Worth::process($action, $id);
        }

        /*
         * Conteo de palabras
         */
        public function wordcount($action = 'list', $id = null) {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Wordcount::process($action, $id);
        }

        /*
         * Elementos en portada
         */
        public function home($action = 'list', $id = null, $type = 'main') {
            $BC = self::menu(array('option'=>__FUNCTION__, 'action' => $action, 'id' => $id));
            define('ADMIN_BCPATH', $BC);
            return Admin\Home::process($action, $id, $type);
        }

        /*
         * Menu de secciones, opciones, acciones y config para el panel Admin
         */
        public static function menu($BC = array()) {

            // si es admin de nodo
            if (isset($_SESSION['admin_node'])) {
                $nodeData = Model\Node::get($_SESSION['admin_node']);
                $admin_label = 'Admin '.$nodeData->name;
            } else {
                $admin_label = 'Admin';
            }

            $options = self::$options;

            // El menu del panel admin dependerá del rol del usuario que accede
            // Superadmin = todo
            // Admin = contenidos de Nodo
            // Supervisor = menus especiales
            if (isset(self::$supervisors[$_SESSION['user']->id])) {
                $menu = self::setMenu('supervisor', $_SESSION['user']->id);
            } elseif (isset($_SESSION['user']->roles['admin'])) {
                $menu = self::setMenu('admin', $_SESSION['user']->id);
            } else {
                $menu = self::setMenu('superadmin', $_SESSION['user']->id);
            }

            // si el breadcrumbs no es un array vacio,
            // devolveremos el contenido html para pintar el camino de migas de pan
            // con enlaces a lo anterior
            if (empty($BC)) {
                return $menu;
            } else {
                // Los últimos serán los primeros
                $path = '';
                
                // si el BC tiene Id, accion sobre ese registro
                // si el BC tiene Action
                if (!empty($BC['action']) && $BC['action'] != 'list') {

                    // si es una accion no catalogada, mostramos la lista
                    if (!in_array(
                            $BC['action'],
                            array_keys($options[$BC['option']]['actions'])
                        )) {
                        $BC['action'] = '';
                        $BC['id'] = null;
                    }

                    $action = $options[$BC['option']]['actions'][$BC['action']];
                    // si es de item , añadir el id (si viene)
                    if ($action['item'] && !empty($BC['id'])) {
                        $path = " &gt; <strong>{$action['label']}</strong> {$BC['id']}";
                    } else {
                        $path = " &gt; <strong>{$action['label']}</strong>";
                    }
                }

                // si el BC tiene Option, enlace a la portada de esa gestión (a menos que sea laaccion por defecto)
                if (!empty($BC['option']) && isset($options[$BC['option']])) {
                    $option = $options[$BC['option']];
                    if ($BC['action'] == 'list') {
                        $path = " &gt; <strong>{$option['label']}</strong>";
                    } else {
                        $path = ' &gt; <a href="/admin/'.$BC['option'].'">'.$option['label'].'</a>'.$path;
                    }
                }

                // si el BC tiene section, facil, enlace al admin
                if ($BC['option'] == 'index') {
                    $path = "<strong>{$admin_label}</strong>";
                } else {
                    $path = '<a href="/admin">'.$admin_label.'</a>' . $path;
                }

                return $path;
            }


        }

        /*
         * Si no tenemos filtros para este gestor los cogemos de la sesion
         */
        private static function setFilters($option) {

            // arary de fltros para el sub controlador
            $filters = array();

            if ($_GET['reset'] == 'filters') {
                unset($_SESSION['admin_filters'][$option]);
                unset($_SESSION['admin_filters']['main']);
                foreach (self::$options[$option]['filters'] as $field=>$default) {
                    $filters[$field] = $default;
                }
                return $filters;
            }

            // si hay algun filtro
            $filtered = false;

            // filtros de este gestor:
            // para cada uno tenemos el nombre del campo y el valor por defecto
            foreach (self::$options[$option]['filters'] as $field=>$default) {
                if (isset($_GET[$field])) {
                    // si lo tenemos en el get, aplicamos ese a la sesión y al array
                    $filters[$field] = (string) $_GET[$field];
                    $_SESSION['admin_filters'][$option][$field] = (string) $_GET[$field];
                    if ( ($option == 'projects' && $field == 'user')
                        || ($option == 'users' && $field == 'name')
                        || ($option == 'accounts' && $field == 'name')
                        || ($option == 'rewards' && $field == 'name') ) {
                        
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
         */
        public static function setMenu($role, $user = null) {

            $options = self::$options;
            
            switch ($role) {
                case 'supervisor':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Gestores',
                            'options' => array ()
                        )
                    );

                    foreach (self::$supervisors[$user] as $opt) {
                        $menu['contents']['options'][$opt] = $options[$opt];
                    }

                    break;
                case 'admin':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Contenidos',
                            'options' => array (
                                'node' => $options['node'],   // la gestion de datos del nodo
                                'pages' => $options['pages'], // páginas institucionales del nodo
                                'blog' => $options['blog'],   // entradas del blog
                                'banners' => $options['banners']    // banners del nodo
                            )
                        ),
                        'projects' => array(
                            'label'   => 'Proyectos',
                            'options' => array (
                                'projects' => $options['projects'],     // proyectos del nodo
                                'reviews' => $options['reviews'],       // revisiones de proyectos del nodo
                                'translates' => $options['translates'], // traducciones de proyectos del nodo
                                'invests' => $options['invests']
                            )
                        ),
                        'users' => array(
                            'label'   => 'Usuarios',
                            'options' => array (
                                'users' => $options['users'],     // usuarios asociados al nodo
                                'mailing' => $options['mailing'], // comunicaciones del nodoc on sus usuarios / promotores
                                'sended' => $options['sended']    // historial de envios realizados por el nodo
                            )
                        ),
                        'home' => array(
                            'label'   => 'Portada',
                            'options' => array (
                                'home' => $options['home'],         // elementos en portada
                                'promote' => $options['promote'],   // seleccion de proyectos destacados
                                'campaigns' => $options['campaigns'],          // convocatorias en portada
                                'blog' => $options['blog'],          // entradas de blog (en la gestion de blog)
                                'sponsors' => $options['sponsors'] // patrocinadores del nodo
                            )
                        )
                    );

                    if ($_SESSION['admin_node'] == \GOTEO_NODE)  {
                        unset($menu['contents']['options']['node']);
                    }
                    
                    break;
                case 'superadmin':
                    $menu = array(
                        'contents' => array(
                            'label'   => 'Textos y Traducciones',
                            'options' => array (
                                'blog' => $options['blog'],
                                'texts' => $options['texts'],
                                'faq' => $options['faq'],
                                'pages' => $options['pages'],
                                'categories' => $options['categories'],
                                'licenses' => $options['licenses'],
                                'icons' => $options['icons'],
                                'tags' => $options['tags'],
                                'criteria' => $options['criteria'],
                                'templates' => $options['templates'],
                                'glossary' => $options['glossary'],
                                'info' => $options['info'],
                                'wordcount' => $options['wordcount']
                            )
                        ),
                        'projects' => array(
                            'label'   => 'Proyectos',
                            'options' => array (
                                'projects' => $options['projects'],
                                'accounts' => $options['accounts'],
                                'patron' => $options['patron'],
                                'reviews' => $options['reviews'],
                                'translates' => $options['translates'],
                                'rewards' => $options['rewards'],
                                'commons' => $options['commons']
                            )
                        ),
                        'users' => array(
                            'label'   => 'Usuarios',
                            'options' => array (
                                'users' => $options['users'],
                                'worth' => $options['worth'],
                                'mailing' => $options['mailing'],
                                'sended' => $options['sended'],
                                'tasks' => $options['tasks']
                            )
                        ),
                        'home' => array(
                            'label'   => 'Portada',
                            'options' => array (
                                'news' => $options['news'],
                                'banners' => $options['banners'],
                                'blog' => $options['blog'],
                                'promote' => $options['promote'],
                                'footer' => $options['footer'],
                                'recent' => $options['recent'],
                                'home' => $options['home']
                            )
                        ),
                        'sponsors' => array(
                            'label'   => 'Servicios',
                            'options' => array (
                                'newsletter' => $options['newsletter'],
                                'sponsors' => $options['sponsors'],
                                'calls' => $options['calls'],
                                'transcalls' => $options['transcalls'],
                                'nodes' => $options['nodes'],
                                'transnodes' => $options['transnodes']
                            )
                        )
                    );
                    break;
            }

            return $menu;
        }


	}

}
