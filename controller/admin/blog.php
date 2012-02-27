<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Feed,
        Goteo\Model;

    class Blog {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            $blog = Model\Blog::get(\GOTEO_NODE, 'node');
            if (!$blog instanceof \Goteo\Model\Blog) {
                $errors[] = 'No tiene espacio de blog, Contacte con nosotros';
                $action = 'list';
            } else {
                if (!$blog->active) {
                    $errors[] = 'Lo sentimos, el blog para este nodo esta desactivado';
                    $action = 'list';
                }
            }

            // primero comprobar que tenemos blog
            if (!$blog instanceof Model\Blog) {
                $errors[] = 'No se ha encontrado ningún blog para este nodo';
                $action = 'list';
            }

            $url = '/admin/blog';

			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    if (empty($_POST['blog'])) {
                        break;
                    }

                    $editing = false;

                    if (!empty($_POST['id'])) {
                        $post = Model\Blog\Post::get($_POST['id']);
                    } else {
                        $post = new Model\Blog\Post();
                    }
                    // campos que actualizamos
                    $fields = array(
                        'id',
                        'blog',
                        'title',
                        'text',
                        'image',
                        'media',
                        'legend',
                        'date',
                        'publish',
                        'home',
                        'footer',
                        'allow'
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
                            $image->remove('post');
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

                    $post->tags = $_POST['tags'];

                    /// este es el único save que se lanza desde un metodo process_
                    if ($post->save($errors)) {
                        if ($action == 'edit') {
                            $success[] = 'La entrada se ha actualizado correctamente';
                            ////Text::get('dashboard-project-updates-saved');
                        } else {
                            $success[] = 'Se ha añadido una nueva entrada';
                            ////Text::get('dashboard-project-updates-inserted');
                            $id = $post->id;
                        }
                        $action = $editing ? 'edit' : 'list';

                        if ((bool) $post->publish) {
                            // Evento Feed
                            $log = new Feed();
                            $log->populate('nueva entrada blog Goteo (admin)', '/admin/blog',
                                \vsprintf('El admin %s ha %s en el blog Goteo la entrada "%s"', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Publicado'),
                                Feed::item('blog', $post->title, $post->id)
                            )));
                            $log->doAdmin('admin');

                            // evento público
                            $log->unique = true;
                            $log->populate($post->title, '/blog/'.$post->id, Text::recorta($post->text, 250), $post->gallery[0]->id);
                            $log->doPublic('goteo');

                            unset($log);
                        } else {
                            //sino lo quitamos
                            \Goteo\Core\Model::query("DELETE FROM feed WHERE url = '/blog/{$post->id}' AND scope = 'public' AND type = 'goteo'");
                        }

                    } else {
                        $errors[] = 'Ha habido algun problema al guardar los datos';
                        ////Text::get('dashboard-project-updates-fail');
                    }
            }

            switch ($action)  {
                case 'remove':
                    // eliminar una entrada
                    $tempData = Model\Blog\Post::get($id);
                    if (Model\Blog\Post::delete($id)) {
                        // Evento Feed
                        $log = new Feed();
                        $log->populate('Quita entrada de blog (admin)', '/admin/blog',
                            \vsprintf('El admin %s ha %s la entrada "%s" del blog de Goteo', array(
                                Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                Feed::item('relevant', 'Quitado'),
                                Feed::item('blog', $tempData->title)
                        )));
                        $log->doAdmin('admin');
                        unset($log);

                        unset($blog->posts[$id]);
                        $success[] = 'Entrada eliminada';
                    } else {
                        $errors[] = 'No se ha podido eliminar la entrada';
                    }
                    // no break para que continue con list
                case 'list':
                    // lista de entradas
                    // obtenemos los datos
                    $posts = Model\Blog\Post::getAll($blog->id, null, false);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'list',
                            'posts' => $posts,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
                case 'add':
                    // nueva entrada con wisiwig
                    // obtenemos datos basicos
                    $post = new Model\Blog\Post(
                            array(
                                'blog' => $blog->id,
                                'date' => date('Y-m-d'),
                                'publish' => false,
                                'allow' => true,
                                'tags' => array()
                            )
                        );

                    $message = 'Añadiendo una nueva entrada';

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'edit',
                            'action' => 'add',
                            'post' => $post,
                            'tags' => Model\Blog\Post\Tag::getAll(),
                            'message' => $message,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        throw new Redirection('/admin/blog');
//                        $errors[] = 'No se ha encontrado la entrada';
                        //Text::get('dashboard-project-updates-nopost');
//                        $action = 'list';
                        break;
                    } else {
                        $post = Model\Blog\Post::get($id);

                        if (!$post instanceof Model\Blog\Post) {
                            $errors[] = 'La entrada esta corrupta, contacte con nosotros.';
                            //Text::get('dashboard-project-updates-postcorrupt');
                            $action = 'list';
                            break;
                        }
                    }

                    $message = 'Editando una entrada existente';

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'edit',
                            'action' => 'edit',
                            'post' => $post,
                            'tags' => Model\Blog\Post\Tag::getAll(),
                            'message' => $message,
                            'errors' => $errors,
                            'success' => $success
                        )
                    );
                    break;
            }

        }

    }

}
