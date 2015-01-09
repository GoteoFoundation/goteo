<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Message,
        Goteo\Model;

    class Node {

        public static function process ($action = 'list', $id = null) {

            if (isset($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) {
                $node = Model\Node::get($_SESSION['admin_node']);
            } else {
                Message::Info('No hay nada que gestionar aquí para Goteo Central');
                throw new Redirection('/admin');
            }

            $langs = \Goteo\Library\Lang::getAll();
            unset($langs['es']);

            $errors = array();

            switch ($action) {
                case 'edit':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                        $fields = array(
                            'name',
                            'subtitle',
                            'email',
                            'location',
                            'description'
                        );

                        foreach ($fields as $field) {
                            if (isset($_POST[$field])) {
                                $node->$field = $_POST[$field];
                            }
                        }

                        // tratar si quitan la imagen
                        if (!empty($_POST['logo-' . $node->logo->hash .  '-remove'])) {
                            if ($node->logo instanceof Model\Image) $node->logo->remove($errors);
                            $node->logo = null;
                        }

                        // tratar la imagen y ponerla en la propiedad logo
                        if(!empty($_FILES['logo_upload']['name'])) {
                            if ($node->logo instanceof Model\Image) $node->logo->remove($errors);
                            $node->logo = $_FILES['logo_upload'];
                        } else {
                            $node->logo = (isset($node->logo->id)) ? $node->logo->id : null;
                        }

                        /// este es el único save que se lanza desde un metodo process_
                        if ($node->update($errors)) {
                            Message::Info('Datos del nodo actualizados correctamente');
                            throw new Redirection('/admin/node');
                        } else {
                            Message::Error('Falló al actualizar los datos del nodo:<br />'.implode('<br />', $errors));
                        }

                    }

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'node',
                            'file' => 'edit',
                            'node' => $node
                        )
                    );
                    break;

                case 'lang':
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lang'])) {
                        $_SESSION['translate_lang'] = $_POST['lang'];
                        Message::Info('Ahora estás traduciendo al <strong>'.$langs[$_SESSION['translate_lang']]->name.'</strong>');
                        throw new Redirection('/admin/node/translate');
                    }
                    break;

                case 'translate':
                    if (empty($_SESSION['translate_lang'])) {
                        $_SESSION['translate_lang'] = 'en';
                    }

                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save-lang'])) {

                        $node->lang_lang = $_POST['lang'];
                        $node->subtitle_lang = $_POST['subtitle'];
                        $node->description_lang = $_POST['description'];

                        /// este es el único save que se lanza desde un metodo process_
                        if ($node->updateLang($errors)) {
                            Message::Info('Traducción del nodo al '.$langs[$_SESSION['translate_lang']].' actualizada correctamente');
                            throw new Redirection('/admin/node');
                        } else {
                            Message::Error('Falló al actualizar la traducción al '.$langs[$_SESSION['translate_lang']]);
                        }

                    }

                    $nodeLang = Model\Node::get($node->id, $_SESSION['translate_lang']);

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'node',
                            'file' => 'translate',
                            'langs' => $langs,
                            'node' => $node,
                            'nodeLang' => $nodeLang
                        )
                    );



                    break;

                case 'admins':
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'node',
                            'file' => 'admins',
                            'node' => $node
                        )
                    );
                    break;

                default:
                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'node',
                            'file' => 'list',
                            'node' => $node
                        )
                    );
            }
        }

    }

}
