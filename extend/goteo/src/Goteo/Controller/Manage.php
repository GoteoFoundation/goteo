<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Feed,
	    Goteo\Library\Text,
	    Goteo\Library\Page,
	    Goteo\Library\Content,
		Goteo\Library\Lang;

	class Manage extends \Goteo\Core\Controller {

        public static $options = array(
            'projects' => array(
                'label' => 'Proyectos (solo en campaña y financiados)',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'preview' => array('label' => 'Datos de contrato del proyecto', 'item' => true),
                    'report' => array('label' => 'Informe proyecto', 'item' => true),
                    'accounts' => array('label' => 'Cuentas proyecto', 'item' => true),
                    'manage' => array('label' => 'Gestionando proyecto', 'item' => true)
                ),
                'filters' => array('status' => '-1', 'projectStatus' => 'all', 'contractStatus' => 'all', 'proj_name' => '', 'owner' => '', 'name' => '', 'node' => '', 'prepay' => '0', 'order' => 'date')
            ),
            /*
            'accounts' => array(
                'label' => 'Aportes delicados (solo con incidencia, manuales, fantasmas y casos conflictivos)',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'manage' => array('label' => 'Gestionando aporte', 'item' => true)
                ),
                'filters' => array('id' => '', 'methods' => '', 'investStatus' => 'all', 'projects' => '', 'name' => '', 'calls' => '', 'review' => '', 'types' => '', 'date_from' => '', 'date_until' => '', 'issue' => 'all', 'procStatus' => 'all', 'amount' => '')
            ),
            */
            'reports' => array(
                'label' => 'Informes (financieros)',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false),
                    'view' => array('label' => 'Viendo informe', 'item' => true)
                ),
                'filters' => array('report' => '', 'from' => '', 'until' => '', 'year' => '', 'status' => '', 'user' => '')
            ),
            'donors' => array(
                'label' => 'Donantes',
                'actions' => array(
                    'list' => array('label' => 'Listando', 'item' => false)
                ),
                'filters' => array('year' => '', 'status' => '', 'user' => '')
            )
        );

        /*
         * Panel para gestionar asuntos financieros y legales
         */
        public function index($option = 'projects', $action = 'list', $id = null, $subaction = null) {

            if ($option == 'index') {
                $BC = self::menu(array('option' => $option, 'action' => null, 'id' => null));
                define('ADMIN_BCPATH', $BC);
                $tasks = Model\Task::getAll(array(), \GOTEO_NODE, true);
                $ret = new View('manage/index.html.php', array('tasks' => $tasks));
            } else {
                $BC = self::menu(array('option' => $option, 'action' => $action, 'id' => $id));
                define('ADMIN_BCPATH', $BC);
                $SubC = 'Goteo\Controller\Manage' . \chr(92) . \ucfirst($option);
                $filters = self::setFilters($option);
                $ret = $SubC::process($action, $id, $subaction, $filters);
            }

            return $ret;
        }

        /*
         *  Menu de secciones, opciones, acciones y config para el panel Manage
         */
        private static function menu($BC = array()) {

            // si el breadcrumbs no es un array vacio,
            //   devolveremos el contenido html para pintar el camino de migas de pan
            //   con enlaces a lo anterior
            $menu = self::$options;

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
                unset($_SESSION['manage_filters'][$option]);
                unset($_SESSION['manage_filters']['main']);
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
                    $_SESSION['manage_filters'][$option][$field] = (string) $_GET[$field];
                    $filtered = true;
                } elseif (!empty($_SESSION['manage_filters'][$option][$field])) {
                    // si no lo tenemos en el get, cogemos de la sesion pero no lo pisamos
                    $filters[$field] = $_SESSION['manage_filters'][$option][$field];
                    $filtered = true;
                } else {
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
