<?php

namespace Goteo\Controller {

	use Goteo\Core\ACL,
        Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Model,
	    Goteo\Library\Text,
		Goteo\Library\Lang;

	class Translate extends \Goteo\Core\Controller {

        public function index () {
            return new View('view/translate/index.html.php');
        }

        public function select ($section = '', $option = '', $id = null) {

            $_SESSION['translator_lang'] = isset($_POST['lang']) ? $_POST['lang'] : GOTEO_DEFAULT_LANG;

            if (!empty($section) && !empty($option)) {
                static::$section($option, $id);
            } else {
                return new View('view/translate/index.html.php');
            }
        }

        /*
         * Gestión de páginas institucionales
         */
		public function pages ($option = 'list', $id = null) {

            $errors = array();

            // si llega post, vamos a guardar los cambios
            if ($option == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
                die('<pre>'.print_r($_POST, 1).'</pre>');

                $page = Page::get($id);
                $page->content = $_POST['content'];
                if ($page->save($errors)) {
                    throw new Redirection("/translate/pages");
                }
            }

            // sino, mostramos para editar
            return new View(
                'view/translate/index.html.php',
                array(
                    'section' => 'pages',
                    'option' => $option,
                    'id' => $id,
                    'errors'=>$errors
                )
             );

		}
        

		public function texts ($option = 'list', $id = null) {
            return new View('view/translate/index.html.php');

            // no cache para textos
            define('GOTEO_translate_NOCACHE', true);

            // comprobamos los filtros
            $filters = array();
            $fields = array('idfilter', 'group', 'text');
            foreach ($fields as $field) {
                if (isset($_GET[$field])) {
                    $filters[$field] = $_GET[$field];
                }
            }

            $filter = "?idfilter={$filters['idfilter']}&group={$filters['group']}&text={$filters['text']}";
            
            // valores de filtro
            $idfilters = Text::filters();
            $groups    = Text::groups();

            // metemos el todos
            \array_unshift($idfilters, 'Todos los textos');
            \array_unshift($groups, 'Todas las agrupaciones');

 //@fixme temporal hasta pasar las agrupaciones a tabal o arreglar en el list.html.php
            $data = Text::getAll($filters);
            foreach ($data as $key=>$item) {
                $data[$key]->group = $groups[$item->group];
            }

            switch ($option) {
                case 'list':
                    return new View(
                        'view/translate/list.html.php',
                        array(
                            'title' => 'Gestión de textos',
                            'menu' => array(),
                            'data' => $data,
                            'columns' => array(
                                'edit' => '',
                                'text' => 'Texto',
                                'group' => 'Agrupación'
                            ),
                            'url' => '/translate/texts',
                            'filters' => array(
                                'idfilter' => array(
                                        'label'   => 'Filtrar por tipo:',
                                        'type'    => 'select',
                                        'options' => $idfilters,
                                        'value'   => $filters['idfilter']
                                    ),
                                'group' => array(
                                        'label'   => 'Filtrar por agrupación:',
                                        'type'    => 'select',
                                        'options' => $groups,
                                        'value'   => $filters['group']
                                    ),
                                'text' => array(
                                        'label'   => 'Buscar texto:',
                                        'type'    => 'input',
                                        'options' => null,
                                        'value'   => $filters['text']
                                    )
                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        $errors = array();

                        $id = $_POST['id'];
                        $text = $_POST['text'];

                        $data = array(
                            'id' => $id,
                            'text' => $_POST['text'],
                            'lang' => \GOTEO_DEFAULT_LANG
                        );

                        if (Text::save($data, $errors)) {
                            throw new Redirection("/translate/texts/$filter");
                        }
                    } else {
                        $text = Text::get($id);
                    }

                    return new View(
                        'view/translate/edit.html.php',
                        array(
                            'title' => "Editando el texto '$id'",
                            'menu' => array(
                                array(
                                    'url'=>'/translate/texts/'.$filter,
                                    'label'=>'Textos'
                                )
                            ),
                            'data' => (object) array (
                                'id' => $id,
                                'text' => $text
                            ),
                            'form' => array(
                                'action' => '/translate/texts/edit/'.$id.'/'.$filter,
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Aplicar'
                                ),
                                'fields' => array (
                                    'idtext' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden',
                                        'properties' => '',

                                    ),
                                    'newtext' => array(
                                        'label' => 'Texto',
                                        'name' => 'text',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="6"',

                                    )
                                )

                            ),
                            'errors' => $errors
                        )
                    );

                    break;
                default:
                    throw new Redirection("/translate");
            }
		}


        /* Contents */
		public function contents ($option = 'list', $id = null) {
            $contents = array (
                'promotes'  => 'Destacados',
                'faq'  => 'FAQ',
                'posts'  => 'Blog',
                'news' => 'Noticias',
                'tags' => 'Tags',
                'icons' => 'Tipos',
                'licenses' => 'Licencias',
                'categories' => 'Categorias/Intereses'
            );


            return new View('view/translate/index.html.php');
        }

        /*
         * proyectos destacados
         */

        /*
         * preguntas frecuentes
         */

        /*
         * criterios de puntuación Goteo
         */

        /*
         * Tipos de Retorno/Recompensa (iconos)
         */

        /*
         * Licencias
         */

        /*
         *  categorias de proyectos / intereses usuarios
         */

        /*
         *  Gestión de tags de blog
         */

        /*
         * Gestión de entradas de blog
         */

        /*
         *  Gestión de noticias
         */

	}

}
