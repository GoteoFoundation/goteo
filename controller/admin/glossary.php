<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Feed,
        Goteo\Model;

    class Glossary {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            $url = '/admin/glossary';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    $editing = false;

                    if (!empty($_POST['id'])) {
                        $post = Model\Glossary::get($_POST['id']);
                    } else {
                        $post = new Model\Glossary();
                    }
                    // campos que actualizamos
                    $fields = array(
                        'id',
                        'title',
                        'text',
                        'media',
                        'legend'
                    );

                    foreach ($fields as $field) {
                        $post->$field = $_POST[$field];
                    }

                    // tratar la imagen y ponerla en la propiedad image
                    if(!empty($_FILES['image_upload']['name'])) {
                        $post->image = $_FILES['image_upload'];
                        $editing = true;
                    }

                    // tratar las imagenes que quitan
                    foreach ($post->gallery as $key=>$image) {
                        if (!empty($_POST["gallery-{$image->id}-remove"])) {
                            $image->remove('glossary');
                            unset($post->gallery[$key]);
                            if ($post->image == $image->id) {
                                $post->image = '';
                            }
                            $editing = true;
                        }
                    }

                    if (!empty($post->media)) {
                        $post->media = new Model\Project\Media($post->media);
                    }

                    /// este es el único save que se lanza desde un metodo process_
                    if ($post->save($errors)) {
                        if ($action == 'edit') {
                            $success[] = 'El término se ha actualizado correctamente';
                        } else {
                            $success[] = 'Se ha añadido un nuevo término';
                            $id = $post->id;
                        }
                        $action = $editing ? 'edit' : 'list';
                    } else {
                        $errors[] = 'Ha habido algun problema al guardar los datos';
                    }
            }

            switch ($action)  {
                case 'remove':
                    // eliminar un término
                    if (Model\Glossary::delete($id)) {
                        $success[] = 'Término eliminado';
                    } else {
                        $errors[] = 'No se ha podido eliminar el término';
                    }
                    break;
                case 'add':
                    // nueva entrada con wisiwig
                    // obtenemos datos basicos
                    $post = new Model\Glossary();

                    $message = 'Añadiendo un nuevo término';

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'glossary',
                            'file' => 'edit',
                            'action' => 'add',
                            'post' => $post,
                            'message' => $message,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        throw new Redirection('/admin/glossary');
                        break;
                    } else {
                        $post = Model\Glossary::get($id);

                        if (!$post instanceof Model\Glossary) {
                            $errors[] = 'La entrada esta corrupta, contacte con nosotros.';
                            //Text::get('dashboard-project-updates-postcorrupt');
                            $action = 'list';
                            break;
                        }
                    }

                    $message = 'Editando un término existente';

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'glossary',
                            'file' => 'edit',
                            'action' => 'edit',
                            'post' => $post,
                            'message' => $message,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
            }

            // lista de términos
            $posts = Model\Glossary::getAll();

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'glossary',
                    'file' => 'list',
                    'posts' => $posts,
                    'errors' => $errors,
                    'success' => $success
                )
            );

        }

    }

}
