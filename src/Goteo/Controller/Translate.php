<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library,
        Goteo\Library\Feed,
	    Goteo\Library\Text,
	    Goteo\Library\Page,
	    Goteo\Library\Content,
		Goteo\Application\Lang;

	class Translate extends \Goteo\Core\Controller {

        /*
         * Para traducir contenidos de nodo, especial: $action = id del nodo; $id = tabla, $auxAction = action, $contentId = registro
         */
        public function index ($table = '', $action = 'list', $id = null, $auxAction = 'list', $contentId = null) {

            // si es un admin le damos todos los idiomas para traducir
            if (isset($_SESSION['user']->roles['admin'])) {

                $langs = Lang::listAll('object', false);
                foreach ($langs as $lang) {
                    $lang = $lang->name;
                }
                $_SESSION['user']->translangs = $langs;

            } else {

                $_SESSION['user']->translangs = Model\User\Translate::getLangs($_SESSION['user']->id);
                if (empty($_SESSION['user']->translangs) ) {

                    Library\Message::Error('No tienes ningún idioma, contacta con el administrador');
                    throw new Redirection('/dashboard');
                }

            }

            if (empty($_SESSION['translate_lang']) || !isset($_SESSION['user']->translangs[$_SESSION['translate_lang']])) {
                if (count($_SESSION['user']->translangs) > 1 && isset($_SESSION['user']->translangs['en'])) {
                    $_SESSION['translate_lang'] = 'en';
                } else {
                    $_SESSION['translate_lang'] = current(array_keys($_SESSION['user']->translangs));
                }
            }

            if (empty($table)) {
                return new View('translate/index.html.php', array('menu'=>self::menu()));
            }

            // para el breadcrumbs segun el contenido
            if (in_array($table, array("news", "promote", "stories", "patron"))) {
                $section = 'home';
            } else {
                $section = 'tables';
            }

            // muy especial para traducción de nodo
            if ($table == 'node') {

                // verificar si este usuario puede traducir este nodo
                // (ojo, al ser  table 'node' el id del nodo está en el parametro $action
                if ( !Model\User\Translate::is_legal($_SESSION['user']->id, $action, 'node') ) {
                    Library\Message::Info(Text::get('user-login-required-access'));
                    throw new Redirection('/dashboard/translates');
                }


                $BC = self::menu(array(
                    'section' => 'node',
                    'node' => $action,
                    'option' => $id,
                    'action' => $auxAction,
                    'id' => $contentId
                ));
            } else {
                $BC = self::menu(array(
                    'section' => $section,
                    'option' => $table,
                    'action' => $action,
                    'id' => $id
                ));
            }

            define('ADMIN_BCPATH', $BC);

            $errors = array();

            // la operación según acción
            switch($table)  {
                case 'texts':
                    return Translate\Texts::process($action, $id, $errors);
                    break;

                case 'node':
                    // ojo parametros especiales
                    return Translate\Node::process($action, $auxAction, $id, $contentId, $errors);
                    break;

                case 'pages':
                    return Translate\Pages::process($action, $id, $errors);
                    break;

                default:
                    // rest of cases, all tables
                    return Translate\Tables::process($table, $action, $id, $contentId, $errors);

            }

            // si no pasa nada de esto, a la portada
            return new View('translate/index.html.php', array('menu'=>self::menu()));
        }

        public function select ($section = '', $action = '', $id = null, $extraAction = null, $extraId = null) {

            $_SESSION['translate_lang'] = isset($_POST['lang']) ? $_POST['lang'] : null;

            if (!empty($section) && !empty($action)) {

                if ($section == 'node') {
                    throw new Redirection("/translate/$section/$action/$id/$extraAction/$extraId");
                }

                $filter = "?type={$_GET['type']}&text={$_GET['text']}&pending={$_GET['pending']}";

                throw new Redirection("/translate/$section/$action/$id/$filter&page=".$_GET['page']);
            } else {
                return new View('translate/index.html.php', array('menu'=>self::menu()));
            }
        }

        /*
         *  Menu de secciones, opciones, acciones y config para el panel Translate
         *
         *  ojo! cambian las options para ser directamente el nombre de la tabla menos para textos y contenidos de página
         * cambian tambien las actions solo list y edit (que es editar la traducción)
         */
        private static function menu($BC = array()) {

            // si el breadcrumbs no es un array vacio,
            //   devolveremos el contenido html para pintar el camino de migas de pan
            //   con enlaces a lo anterior

            $menu = Translate\Menu::get();

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
                        if (!empty($BC['node'])) {
                            $path = ' &gt; <a href="/translate/node/'.$BC['node'].'/'.$BC['option'].'">'.$option['label'].'</a>'.$path;
                        } else {
                            $path = ' &gt; <a href="/translate/'.$BC['option'].''.$BC['filter'].'">'.$option['label'].'</a>'.$path;
                        }
                    }
                }

                if (empty($BC['option'])) {
                    if (!empty($BC['node'])) {
                        $path = 'Traduciendo nodo <strong>'.ucfirst($BC['node']).'</strong>';
                    } else {
                        $path = '<strong>Traductor</strong>';
                    }
                } else {
                    if (!empty($BC['node'])) {
                        $path = '<a href="/translate/node/'.$BC['node'].'">Traduciendo nodo <strong>'.$BC['node'].'</strong></a>' . $path;
                    } else {
                        $path = '<a href="/translate">Traductor</a>' . $path;
                    }
                }

                return $path;
            }


        }


	}

}
