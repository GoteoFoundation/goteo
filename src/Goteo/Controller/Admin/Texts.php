<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
	    Goteo\Library\Text,
	    Goteo\Application\Message,
		Goteo\Library\Feed;

    class Texts {

        public static function process ($action = 'list', $id = null, $filters = array()) {


            // valores de filtro
            $groups    = Text::groups();

            // metemos el todos
            \array_unshift($groups, 'Todas las agrupaciones');

 //@fixme temporal hasta pasar las agrupaciones a tabal o arreglar en el list.html.php
            $data = Text::getAll($filters, 'original');
            foreach ($data as $key=>$item) {
                $data[$key]->group = $groups[$item->group];
            }

            switch ($action) {
                case 'list':
                    return new View(
                        'admin/index.html.php',
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
                                'filtered' => $filters['filtered'],
                                'group' => array(
                                        'label'   => 'Filtrar:',
                                        'type'    => 'select',
                                        'options' => $groups,
                                        'value'   => $filters['group']
                                    ),
                                'text' => array(
                                        'label'   => 'Texto:',
                                        'type'    => 'input',
                                        'options' => null,
                                        'value'   => $filters['text']
                                    )
                                /*,
                                'idfilter' => array(
                                        'label'   => 'Id:',
                                        'type'    => 'input',
                                        'options' => null,
                                        'value'   => $filters['idfilter']
                                    )*/
                            )
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
                            Message::Info('El texto ha sido actualizado');

                            // tratar si han marcado pendiente de traducir
                            // no usamos Core\Model porque no es tabla _lang
                            if (isset($_POST['pending']) && $_POST['pending'] == 1) {
                                $ok = Text::setPending($id, $errors);
                                if (!$ok) {
                                    Message::Error(implode('<br />', $errors));
                                }
                            }


                            throw new Redirection("/admin/texts");
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }
                    } else {
                        $text = Text::getPurpose($id);
                    }

                    return new View(
                        'admin/index.html.php',
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

                            )
                        )
                    );

                    break;
                default:
                    throw new Redirection("/admin");
            }

        }

    }

}
