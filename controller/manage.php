<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Feed,
	    Goteo\Library\Message,
	    Goteo\Library\Text,
	    Goteo\Library\Page,
	    Goteo\Library\Content,
		Goteo\Library\Lang;

	class Manage extends \Goteo\Core\Controller {

        /*
         * Panel para gestionar asuntos financieros y legales
         */
        public function index($option = 'index', $action = 'list', $id = null, $subaction = null) {
            if ($option == 'index') {
                $BC = self::menu(array('option' => $option, 'action' => null, 'id' => null));
                define('ADMIN_BCPATH', $BC);
                $node = isset($_SESSION['admin_node']) ? $_SESSION['admin_node'] : \GOTEO_NODE;
                $tasks = Model\Task::getAll(array(), $node, true);
                return new View('view/manage/index.html.php', array('tasks' => $tasks));
            } else {
                $BC = self::menu(array('option' => $option, 'action' => $action, 'id' => $id));
                define('ADMIN_BCPATH', $BC);
                $SubC = 'Goteo\Controller\Manage' . \chr(92) . \ucfirst($option);
                return $SubC::process($action, $id, self::setFilters($option), $subaction);
            }
        }

        /*
         *  Menu de secciones, opciones, acciones y config para el panel Manage
         */
        private static function menu($BC = array()) {

            // si el breadcrumbs no es un array vacio,
            //   devolveremos el contenido html para pintar el camino de migas de pan
            //   con enlaces a lo anterior

            $menu = array(
                'financial' => array(
                    'label'   => 'Gestión financiera',
                    'options' => array (
                        'projects' => array(
                            'label' => 'Proyectos (solo en campaña y financiados)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'manage' => array('label' => 'Gestionando proyecto', 'item' => true)
                            )
                        ),
                        'campaigns' => array(
                            'label' => 'Convocatorias (solo asuntos económicos)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'manage' => array('label' => 'Gestionando convocatoria', 'item' => true)
                            )
                        ),
                        'accounts' => array(
                            'label' => 'Aportes delicados (solo con incidencia, manuales, fantasmas y casos conflictivos)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'manage' => array('label' => 'Gestionando aporte', 'item' => true)
                            )
                        ),
                        'reports' => array(
                            'label' => 'Informes (financieros)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'view' => array('label' => 'Viendo informe', 'item' => true)
                            )
                        )
                    )
                ),
                'legal' => array(
                    'label'   => 'Asuntos legales',
                    'options' => array (
                        'users' => array(
                            'label' => 'Impulsores (solo impulsores de proyectos publicados)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'manage' => array('label' => 'Gestionando impulsor', 'item' => true)
                            )
                        ),
                        'contracts' => array(
                            'label' => 'Contratos (para control de proceso)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'manage' => array('label' => 'Gestionando contrato', 'item' => true)
                            )
                        ),
                        'certificates' => array(
                            'label' => 'Certificados (exportación)',
                            'actions' => array(
                                'list' => array('label' => 'Listando', 'item' => false),
                                'manage' => array('label' => 'Gestionando certificado', 'item' => true)
                            )
                        )
                    )
                )
            );

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
                        $BC['action'] = '';
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
                if (!empty($BC['option']) && isset($menu[$BC['section']]['options'][$BC['option']])) {
                    $option = $menu[$BC['section']]['options'][$BC['option']];
                    if ($BC['action'] == 'list') {
                        $path = " &gt; <strong>{$option['label']}</strong>";
                    } else {
                        $path = ' &gt; <a href="/manage/'.$BC['option'].''.$BC['filter'].'">'.$option['label'].'</a>'.$path;
                    }
                }

                if (empty($BC['option'])) {
                    $path = '<strong>'.Text::get('regular-manage_board').'</strong>';
                } else {
                    $path = '<a href="/manage">'.Text::get('regular-manage_board').'</a>' . $path;
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
                        case 'contracts':
                            if ($field == 'name' && !empty($_SESSION['admin_filters']['main']['user_name'])) {
                                $filters['name'] = $_SESSION['admin_filters']['main']['user_name'];
                                $filtered = true;
                            }
                            break;
                        case 'certificates':
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

	}

}
