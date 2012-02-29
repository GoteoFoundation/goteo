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
        Goteo\Library\Worth;

	class Admin extends \Goteo\Core\Controller {

        public function index () {
            return new View('view/admin/index.html.php', array('menu'=>self::menu()));
        }

        public function select () {

            $_SESSION['translator_lang'] = isset($_POST['lang']) ? $_POST['lang'] : null;

            return new View('view/admin/index.html.php', array('menu'=>self::menu()));
        }

        /*
         * Info de Actividad reciente para los administradores
         */
        public function feed () {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => 'list'
            ));

            define('ADMIN_BCPATH', $BC);

            return new View('view/admin/feed.html.php');
        }

        /*
         * Gestión de páginas institucionales
         */
		public function pages ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Pages::process($action, $id);
		}

        /*
         * Gestion de textos dinámicos
         */
		public function texts ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            // no cache para textos
            define('GOTEO_ADMIN_NOCACHE', true);

            return Admin\Texts::process($action, $id);
		}

        /*
         * Gestión de plantillas para emails automáticos
         */
		public function templates ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Templates::process($action, $id);
		}

        /*
         *  Lista de proyectos
         */
        public function projects($action = 'list', $id = null) {

            $log_text = null;

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Projects::process($action, $id);
        }

        /*
         *  Revision de proyectos
         */
        public function reviews($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Reviews::process($action, $id);
        }

        /*
         *  Traducciones de proyectos
         */
        public function translates($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Translates::process($action, $id);
        }

        /*
         *  Traducciones de convocatorias
         */
        public function transcalls($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Transcalls::process($action, $id);
        }

        /*
         * proyectos destacados
         */
        public function promote($action = 'list', $id = null, $flag = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Promote::process($action, $id, $flag);
        }

        /*
         * proyectos recomendados por padrinos
         */
        public function patron($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Patron::process($action, $id);
        }

        /*
         * Banners
         */
        public function banners($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Banners::process($action, $id);
        }

        /*
         * preguntas frecuentes
         */
        public function faq($action = 'list', $id = null) {

            // secciones para verificar filtro y filtro por defecto
            $sections = Model\Faq::sections();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'node';
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => '?filter=' . $filter
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Faq::process($action, $id, $filter);
        }

        /*
         * criterios de puntuación Goteo
         */
        public function criteria($action = 'list', $id = null) {

            // secciones para verificar filtro y filtro por defecto
            $sections = Model\Criteria::sections();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $sections)) {
                $filter = $_GET['filter'];
            } else {
                $filter = 'project';
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => '?filter=' . $filter
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Criteria::process($action, $id, $filter);
        }

        /*
         * Tipos de Retorno/Recompensa (iconos)
         */
        public function icons($action = 'list', $id = null) {

            // grupos para verificar filtro
            $groups = Model\Icon::groups();
            if (isset($_GET['filter']) && array_key_exists($_GET['filter'], $groups)) {
                $filter = $_GET['filter'];
            } else {
                $filter = '';
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filter) ? '?filter=' . $filter : ''
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Icons::process($action, $id, $filter);
        }

        /*
         * Licencias
         */
        public function licenses($action = 'list', $id = null) {

            if (isset($_GET['filters'])) {
                foreach (\unserialize($_GET['filters']) as $field=>$value) {
                    $filters[$field] = $value;
                }
            } else {
                $filters = array();
            }

            $fields = array('group', 'icon');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? '?filter=' . serialize($filters) : ''
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Licenses::process($action, $id, $filters);
        }

        /*
         * posts para portada
         */
        public function posts($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Posts::process($action, $id);
        }

        /*
         * posts para pie
         */
        public function footer($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Footer::process($action, $id);
        }

        /*
         *  Gestión de categorias de proyectos
         *  Si no la usa nadie se puede borrar
         */
        public function categories($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Categories::process($action, $id);
        }

        /*
         *  Gestión de tags de blog
         *  Si no lo usa ningun post se puede borrar
         *  Si es un nodo solamente puede borrar los propios
         */
        public function tags($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Tags::process($action, $id);
        }

        /*
         *  administración de usuarios para superadmin
         */
        public function users($action = 'list', $id = null, $subaction = '') {

            $filters = array();
            $fields = array('status', 'interest', 'role', 'id', 'name', 'email', 'order');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $BC = self::menu(array(
                'section' => 'users',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? "?status={$filters['status']}&interest={$filters['interest']}&role={$filters['role']}&name={$filters['name']}&order={$filters['order']}" : ''
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Users::process($action, $id, $subaction, $filters);
        }

        /*
         *  Gestión de aportes a proyectos
         */
        public function invests($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'accounting',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Invests::process($action, $id);
        }

        /*
         *  Gestión transacciones (tpv/paypal)
         *  solo proyectos en campaña o financiados
         * 
         */
        public function accounts($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'accounting',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Accounts::process($action, $id);
        }


        /*
         * Gestión de retornos, por ahora en el admin pero es una gestión para los responsables de proyectos
         * Proyectos financiados, puede marcar un retorno cumplido
         */
        public function rewards($action = 'list', $id = null) {

            $filters = array();
            $fields = array('status', 'icon');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $BC = self::menu(array(
                'section' => 'projects',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? "?status={$filters['status']}&icon={$filters['icon']}" : ''
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Rewards::process($action, $id, $filters);
        }

        /*
         * Gestión de entradas de blog
         */
        public function blog ($action = 'list', $id = null) {
            
            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Blog::process($action, $id);
        }

        /*
         * Gestión de términos del Glosario
         */
        public function glossary ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Glossary::process($action, $id);
        }

        /*
         * Gestión de entradas de info
         */
        public function info ($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Info::process($action, $id);
        }


        /*
         *  Gestión de noticias
         */
        public function news($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\News::process($action, $id);
        }

        /*
         *  Gestión de patrocinadores
         */
        public function sponsors($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Sponsors::process($action, $id);
        }

        /*
         *  Lista de convocatorias
         */
        public function calls($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'sponsors',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Sponsors::process($action, $id);
        }

        /*
         *  Gestión de nodos
         */
        public function nodes($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'nodes',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => !empty($filters) ? "?status={$filters['status']}&admin={$filters['admin']}&name={$filters['name']}" : ''
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Nodes::process($action, $id);
        }

        /*
         * Comunicaciones con los usuarios mediante mailing
         */
        public function mailing($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'users',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Mailing::process($action, $id);
        }

        /*
         *  historial de emails enviados
         */
        public function sended($action = 'list') {

            $BC = self::menu(array(
                'section' => 'users',
                'option' => __FUNCTION__,
                'action' => $action
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Sended::process($action, $id);
        }

        /*
         * Niveles de meritocracia
         */
        public function worth($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'users',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Worth::process($action, $id);
        }

        /*
         * Conteo de palabras
         */
        public function wordcount($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'contents',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Wordcount::process($action, $id);
        }

        /*
         * Elementos en portada
         */
        public function home($action = 'list', $id = null) {

            $BC = self::menu(array(
                'section' => 'home',
                'option' => __FUNCTION__,
                'action' => $action,
                'id' => $id,
                'filter' => ''
            ));

            define('ADMIN_BCPATH', $BC);

            return Admin\Home::process($action, $id);
        }


        /*
         * Menu de secciones, opciones, acciones y config para el panel Admin
         *
         */
        private static function menu($BC = array()) {

            // si el breadcrumbs no es un array vacio,
            //   devolveremos el contenido html para pintar el camino de migas de pan
            //   con enlaces a lo anterior

            // Primero montamos un array de los gestores que existen
            $options = array(
                        'blog' => array(
                            'label' => 'Blog',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Entrada', 'item' => false),
                                'edit' => array('label' => 'Editando Entrada', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Entrada', 'item' => true)
                            )
                        ),
                        'texts' => array(
                            'label' => 'Textos interficie',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Original', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Texto', 'item' => true)
                            )
                        ),
                        'faq' => array(
                            'label' => 'FAQs',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Pregunta', 'item' => false),
                                'edit' => array('label' => 'Editando Pregunta', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Pregunta', 'item' => true)
                            )
                        ),
                        'pages' => array(
                            'label' => 'Páginas institucionales',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Página', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Página', 'item' => true)
                            )
                        ),
                        'categories' => array(
                            'label' => 'Categorias e Intereses',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Categoría', 'item' => false),
                                'edit' => array('label' => 'Editando Categoría', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Categoría', 'item' => true)
                            )
                        ),
                        'licenses' => array(
                            'label' => 'Licencias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Licencia', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Licencia', 'item' => true)
                            )
                        ),
                        'icons' => array(
                            'label' => 'Tipos de Retorno',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Tipo', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Tipo', 'item' => true)
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
                        'criteria' => array(
                            'label' => 'Criterios de revisión',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Criterio', 'item' => false),
                                'edit' => array('label' => 'Editando Criterio', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Criterio', 'item' => true)
                            )
                        ),
                        'templates' => array(
                            'label' => 'Plantillas de email',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Plantilla', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Plantilla', 'item' => true)
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
                        'info' => array(
                            'label' => 'Ideas about',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Idea', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Idea', 'item' => true)
                            )
                        ),
                        'wordcount' => array(
                            'label' => 'Conteo de palabras',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false)
                            )
                        ),
                        'projects' => array(
                            'label' => 'Listado de proyectos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'dates' => array('label' => 'Cambiando las fechas del proyecto ', 'item' => true),
                                'accounts' => array('label' => 'Gestionando las cuentas del proyecto ', 'item' => true)
                            )
                        ),
                        'reviews' => array(
                            'label' => 'Revisiones',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Iniciando briefing', 'item' => false),
                                'edit' => array('label' => 'Editando briefing', 'item' => true),
                                'report' => array('label' => 'Informe', 'item' => true)
                            )
                        ),
                        'translates' => array(
                            'label' => 'Traducciones de proyectos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Habilitando traducción', 'item' => false),
                                'edit' => array('label' => 'Asignando traducción', 'item' => true)
                            )
                        ),
                        'rewards' => array(
                            'label' => 'Gestión de retornos colectivos cumplidos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false)
                            )
                        ),
                        'users' => array(
                            'label' => 'Listado de usuarios',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add' => array('label' => 'Creando Usuario', 'item' => true),
                                'edit' => array('label' => 'Editando Usuario', 'item' => true),
                                'manage' => array('label' => 'Gestionando Usuario', 'item' => true),
                                'impersonate' => array('label' => 'Suplantando al Usuario', 'item' => true)
                            )
                        ),
                        'worth' => array(
                            'label' => 'Niveles de meritocracia',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'edit' => array('label' => 'Editando Nivel', 'item' => true)
                            )
                        ),
                        'mailing' => array(
                            'label' => 'Comunicaciones',
                            'actions' => array(
                                'list' => array('label' => 'Seleccionando destinatarios', 'item' => false),
                                'edit' => array('label' => 'Escribiendo contenido', 'item' => false),
                                'send' => array('label' => 'Comunicación enviada', 'item' => false)
                            )
                        ),
                        'sended' => array(
                            'label' => 'Historial envios',
                            'actions' => array(
                                'list' => array('label' => 'Emails enviados', 'item' => false)
                            )
                        ),
                        'invests' => array(
                            'label' => 'Aportes a Proyectos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Aporte manual', 'item' => false),
                                'move'  => array('label' => 'Reubicando el aporte', 'item' => true),
                                'details' => array('label' => 'Detalles del aporte', 'item' => true),
                                'execute' => array('label' => 'Ejecución del cargo ahora mismo', 'item' => true),
                                'cancel' => array('label' => 'Cancelando aporte', 'item' => true),
                                'report' => array('label' => 'Informe de proyecto', 'item' => true)
                            )
                        ),
                        'accounts' => array(
                            'label' => 'Transacciones económicas',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'details' => array('label' => 'Detalles de la transacción', 'item' => true),
                                'viewer' => array('label' => 'Viendo logs', 'item' => false)
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
                        'banners' => array(
                            'label' => 'Banners',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Banner', 'item' => false),
                                'edit' => array('label' => 'Editando Banner', 'item' => true),
                                'translate' => array('label' => 'Traduciendo Banner', 'item' => true)
                            )
                        ),
                        'posts' => array(
                            'label' => 'Entradas de blog',
                            'actions' => array(
                                'list' => array('label' => 'Ordenando', 'item' => false),
                                'add'  => array('label' => 'Colocando Entrada en la portada', 'item' => false)
                            )
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
                        'footer' => array(
                            'label' => 'Entradas en el footer',
                            'actions' => array(
                                'list' => array('label' => 'Ordenando', 'item' => false),
                                'add'  => array('label' => 'Colocando Entrada en el footer', 'item' => false)
                            )
                        ),
                        'feed' => array(
                            'label' => 'Actividad reciente',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false)
                            )
                        ),
                        'home' => array(
                            'label' => 'Elementos en portada',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false)
                            )
                        ),
                        'calls' => array(
                            'label' => 'Listado de convocatorias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva convocatoria', 'item' => false),
                                'projects' => array('label' => 'Gestionando proyectos de la convocatoria', 'item' => true)
                            )
                        ),
                        'transcalls' => array(
                            'label' => 'Traducciones de convocatorias',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Habilitando traducción', 'item' => false),
                                'edit' => array('label' => 'Asignando traducción', 'item' => true)
                            )
                        ),
                        'sponsors' => array(
                            'label' => 'Apoyos institucionales',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Patrocinador', 'item' => false),
                                'edit' => array('label' => 'Editando Patrocinador', 'item' => true)
                            )
                        ),
                        'patron' => array(
                            'label' => 'Proyectos apadrinados',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nueva Recomendación', 'item' => false),
                                'edit' => array('label' => 'Editando Recomendacion', 'item' => true)
                            )
                        ),
                        'nodes' => array(
                            'label' => 'Gestión de Nodos',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'add'  => array('label' => 'Nuevo Nodo', 'item' => false),
                                'edit' => array('label' => 'Gestionando Nodo', 'item' => true)
                            )
                        ),
                        'node' => array(
                            'label' => 'Datos del Nodo',
                            'actions' => array(
                                'edit' => array('label' => 'Gestionando Nodo', 'item' => true)
                            )
                        )
                    );

            // El menu del panel admin dependerá del rol del usuario que accede
            // Superadmin = todo
            // Admin = contenidos de Nodo
            if (isset($_SESSION['admin_node'])) {
                $menu = array(
                    'contents' => array(
                        'label'   => 'Contenidos',
                        'options' => array (
                            'node' => $options['node'],   // la gestion de datos del nodo
                            'pages' => $options['pages'], // páginas institucionales del nodo
                            'sponsors' => $options['sponsors'], // patrocinadores del nodo
                            'blog' => $options['blog'],   // entradas del blog
                            'tags' => $options['tags']    // tags de blog
                        )
                    ),
                    'projects' => array(
                        'label'   => 'Gestión de proyectos',
                        'options' => array (
                            'projects' => $options['projects'],     // proyectos del nodo
                            'reviews' => $options['reviews'],       // revisiones de proyectos del nodo
                            'translates' => $options['translates'], // traducciones de proyectos del nodo
                            'patron' => $options['patron']          // padrinos de proyectos del nodo
                        )
                    ),
                    'users' => array(
                        'label'   => 'Gestión de usuarios',
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
                            'posts' => $options['posts'],       // seleccion de entradas en portada
                            'promote' => $options['promote'],   // seleccion de proyectos destacados
                            // convocatorias en portada del nodo
                            'feed' => $options['feed']          // Eventos de admin (a ver cuales le van a salir)
                        )
                    )
                );
            } else {
                $menu = array(
                    'contents' => array(
                        'label'   => 'Gestión de Textos y Traducciones',
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
                        'label'   => 'Gestión de proyectos',
                        'options' => array (
                            'projects' => $options['projects'],
                            'reviews' => $options['reviews'],
                            'translates' => $options['translates'],
                            'rewards' => $options['rewards'],
                            'patron' => $options['patron']
                        )
                    ),
                    'users' => array(
                        'label'   => 'Gestión de usuarios',
                        'options' => array (
                            'users' => $options['users'],
                            'worth' => $options['worth'],
                            'mailing' => $options['mailing'],
                            'sended' => $options['sended']
                        )
                    ),
                    'accounting' => array(
                        'label'   => 'Gestión de aportes y transacciones',
                        'options' => array (
                            'invests' => $options['invests'],
                            'accounts' => $options['accounts']
                        )
                    ),
                    'home' => array(
                        'label'   => 'Portada',
                        'options' => array (
                            'news' => $options['news'],
                            'banners' => $options['banners'],
                            'posts' => $options['posts'],
                            'promote' => $options['promote'],
                            'footer' => $options['footer'],
                            'feed' => $options['feed'],
                            'home' => $options['home']
                        )
                    ),
                    'sponsors' => array(
                        'label'   => 'Convocatorias y patrocinadores',
                        'options' => array (
                            'calls' => $options['calls'],
                            'transcalls' => $options['transcalls'],
                            'sponsors' => $options['sponsors']
                        )
                    ),
                    'nodes' => array(
                        'label'   => 'Nodos',
                        'options' => array (
                            'nodes' => $options['nodes']
                        )
                    )
                );
            }

            if (empty($BC)) {
                return $menu;
            } else {
                // Los últimos serán los primeros
                $path = '';
                
                // si el BC tiene Id, accion sobre ese registro
                // si el BC tiene Action
                if (!empty($BC['action'])) {

                    // si es una accion no catalogada, mostramos la lista
                    if (!in_array(
                            $BC['action'],
                            array_keys($menu[$BC['section']]['options'][$BC['option']]['actions'])
                        )) {
                        $BC['action'] = 'list';
                        $BC['id'] = null;
                    }

                    $action = $menu[$BC['section']]['options'][$BC['option']]['actions'][$BC['action']];
                    // si es de item , añadir el id (si viene)
                    if ($action['item'] && !empty($BC['id'])) {
                        $path = " &gt; <strong>{$action['label']}</strong> {$BC['id']}";
                    } else {
                        $path = " &gt; <strong>{$action['label']}</strong>";
                    }
                }

                // si el BC tiene Option, enlace a la portada de esa gestión
                if (!empty($BC['option'])) {
                    $option = $menu[$BC['section']]['options'][$BC['option']];
                    $path = ' &gt; <a href="/admin/'.$BC['option'].''.$BC['filter'].'">'.$option['label'].'</a>'.$path;
                }

                // si el BC tiene section, facil, enlace al admin
                if (!empty($BC['section'])) {
                    $section = $menu[$BC['section']];
                    $path = '<a href="/admin#'.$BC['section'].'">'.$section['label'].'</a>' . $path;
                }
                return $path;
            }


        }


	}

}
