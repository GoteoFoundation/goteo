<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Application\Message,
		Goteo\Library\Feed,
        Goteo\Model;

    class Info {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            $url = '/admin/info';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                    $editing = false;

                    if (!empty($_POST['id'])) {
                        $post = Model\Info::get($_POST['id']);
                    } else {
                        $post = new Model\Info();
                    }
                    // campos que actualizamos
                    $fields = array(
                        'id',
                        'node',
                        'title',
                        'text',
                        'media',
                        'legend',
                        'publish',
                        'order'
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
                        if (!empty($_POST["gallery-{$image->hash}-remove"])) {
                            $image->remove($errors, 'info');
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
                            Message::Info('La entrada se ha actualizado correctamente');

                            if ((bool) $post->publish) {
                                $log_action = 'Publicado';
                            } else {
                                $log_action = 'Modificado';
                            }

                        } else {
                            Message::Info('Se ha añadido una nueva entrada');
                            $id = $post->id;
                            $log_action = 'Añadido';
                        }
                        $action = $editing ? 'edit' : 'list';

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('modificacion de idea about (admin)', '/admin/info',
                            \vsprintf('El admin %s ha %s la Idea de fuerza "%s"', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', $log_action),
                                Feed::item('relevant', $post->title, '/about#info'.$post->id)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                        // tratar si han marcado pendiente de traducir
                        if (isset($_POST['pending']) && $_POST['pending'] == 1
                            && !Model\Info::setPending($post->id, 'post')) {
                            Message::Error('NO se ha marcado como pendiente de traducir!');
                        }

                    } else {
                        Message::Error(implode('<br />', $errors));
                        Message::Error('Ha habido algun problema al guardar los datos');
                    }
            }

            switch ($action)  {
                case 'up':
                    Model\Info::up($id);
                    break;
                case 'down':
                    Model\Info::down($id);
                    break;
                case 'remove':
                    $tempData = Model\Info::get($id);
                    // eliminar un término
                    if (Model\Info::delete($id)) {
                        Message::Info('Entrada eliminada');

                        // Evento Feed
                        $log = new Feed();
                        $log->populate('quitar de idea about (admin)', '/admin/info',
                            \vsprintf('El admin %s ha %s la Idea de fuerza "%s"', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Eliminado'),
                                Feed::item('relevant', $tempData->title)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                    } else {
                        Message::Error('No se ha podido eliminar la entrada');
                    }
                    break;
                case 'add':
                    // nueva entrada con wisiwig
                    // obtenemos datos basicos
                    if (!$post instanceof Model\Info) {
                        $post = new Model\Info();
                    }

                    $message = 'Añadiendo una nueva entrada';

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'info',
                            'file' => 'edit',
                            'action' => 'add',
                            'post' => $post,
                            'message' => $message
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        throw new Redirection('/admin/info');
                        break;
                    } else {
                        $post = Model\Info::get($id);

                        if (!$post instanceof Model\Info) {
                            Message::Error('La entrada esta corrupta, contacte con nosotros.');
                            $action = 'list';
                            break;
                        }
                    }

                    $message = 'Editando una entrada existente';

                    return new View(
                        'admin/index.html.php',
                        array(
                            'folder' => 'info',
                            'file' => 'edit',
                            'action' => 'edit',
                            'post' => $post,
                            'message' => $message
                        )
                    );
                    break;
            }

            // lista de términos
            $posts = Model\Info::getAll();

            return new View(
                'admin/index.html.php',
                array(
                    'folder' => 'info',
                    'file' => 'list',
                    'posts' => $posts
                )
            );

        }

    }

}
