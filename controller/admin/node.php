<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
        Goteo\Model;

    class Node {

        public static function process () {

            if (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) {
                $node = Model\Node::get($_SESSION['admin_node']);
            } else {
                Message::Info('No hay nada que gestionar aquí para el Master Node GOTEO');
                throw new Redirection('/admin');
            }

            $errors = array();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                // tratar la imagen y ponerla en la propiedad logo
                // __FILES__

                $fields = array(
                    'name',
                    'subtitle',
                    'location',
                    'description'
                );

                foreach ($fields as $field) {
                    if (isset($_POST[$field])) {
                        $node->$field = $_POST[$field];
                    }
                }

                // tratar si quitan la imagen
                if (!empty($_POST['logo-' . $node->logo->id .  '-remove'])) {
                    $node->logo->remove('node');
                    $node->logo = '';
                }

                // logo
                if(!empty($_FILES['logo_upload']['name'])) {
                    $node->logo = $_FILES['logo_upload'];
                }

                /// este es el único save que se lanza desde un metodo process_
                if ($node->update($errors)) {
                    Message::Info('Datos del nodo actualizados correctamente');
                    throw new Redirection('/admin');
                } else {
                    Message::Error('Falló al actualizar los datos del nodo:<br />'.implode('<br />', $errors));
                }
			}

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'node',
                    'file' => 'edit',
                    'node' => $node
                )
            );
            
        }

    }

}
