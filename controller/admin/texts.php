<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Feed,
	    Goteo\Library\Text;

    class Texts {

        public static function process ($action = 'list', $id = null, $filters = array()) {
            

            // valores de filtro
            $idfilters = Text::filters();
            $groups    = Text::groups();

            // metemos el todos
            \array_unshift($idfilters, 'Todos los textos');
            \array_unshift($groups, 'Todas las agrupaciones');

 //@fixme temporal hasta pasar las agrupaciones a tabal o arreglar en el list.html.php
            $data = Text::getAll($filters, 'original');
            foreach ($data as $key=>$item) {
                $data[$key]->group = $groups[$item->group];
            }

            switch ($action) {
                case 'list':
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'list',
                            'data' => $data,
                            'columns' => array(
                                'edit' => '',
                                'text' => 'Texto',
                                'group' => 'Agrupación'
                            ),
                            'url' => '/admin/texts',
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
                            'text' => $_POST['text']
                        );

                        if (Text::update($data, $errors)) {
                            throw new Redirection("/admin/texts");
                        }
                    } else {
                        $text = Text::getPurpose($id);
                    }

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'texts',
                            'file' => 'edit',
                            'data' => (object) array (
                                'id' => $id,
                                'text' => $text
                            ),
                            'form' => array(
                                'action' => '/admin/texts/edit/'.$id,
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
                    throw new Redirection("/admin");
            }
            
        }

    }

}
