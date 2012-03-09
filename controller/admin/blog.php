<?php

namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error,
		Goteo\Library\Text,
		Goteo\Library\Feed,
		Goteo\Library\Message,
        Goteo\Model;

    class Blog {

        public static function process ($action = 'list', $id = null) {

            $errors = array();

            $blog = Model\Blog::get(\GOTEO_NODE, 'node');
            if (!$blog instanceof \Goteo\Model\Blog) {
                Message::Error('No tiene espacio de blog, Contacte con nosotros');
                $action = 'list';
            } else {
                if (!$blog->active) {
                    Message::Error('Lo sentimos, el blog para este nodo esta desactivado');
                    $action = 'list';
                }
            }

            // primero comprobar que tenemos blog
            if (!$blog instanceof Model\Blog) {
                Message::Error('No se ha encontrado ningún blog para este nodo');
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
                            Message::Info('La entrada se ha actualizado correctamente');
                            ////Text::get('dashboard-project-updates-saved');
                        } else {
                            Message::Info('Se ha añadido una nueva entrada');
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
                        Message::Error('Ha habido algun problema al guardar los datos:<br />' . \implode('<br />', $errors));
                    }
            }

            switch ($action)  {
                case 'remove':
                    // eliminar una entrada
                    $tempData = Model\Blog\Post::get($id);
                    if ($tempData->owner != 'node-'.$_SESSION['admin_node']) {
                        Message::Error('No puedes eliminar esta entrada.');
                        throw new Redirection('/admin/blog/list');
                    }
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

                        Message::Info('Entrada eliminada');
                    } else {
                        Message::Error('No se ha podido eliminar la entrada');
                    }
                    throw new Redirection('/admin/blog/list');
                    break;
                case 'list':
                    // lista de entradas
                    // obtenemos los datos
                    $posts = Model\Blog\Post::getAll($blog->id, null, false);
                    $home = Model\Post::getList('home', $_SESSION['admin_node']);
                    $footer = Model\Post::getList('footer', $_SESSION['admin_node']);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'list',
                            'posts' => $posts,
                            'home' => $home,
                            'footer' => $footer
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
                            'message' => $message
                        )
                    );
                    break;
                case 'edit':
                    if (empty($id)) {
                        Message::Error('No se ha encontrado la entrada');
                        throw new Redirection('/admin/blog');
                        break;
                    } else {
                        $post = Model\Blog\Post::get($id);

                        if (!$post instanceof Model\Blog\Post) {
                            Message::Error('La entrada esta corrupta, contacte con nosotros.');
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
                            'message' => $message
                        )
                    );
                    break;

                // acciones portada
                case 'reorder':
                    // lista de entradas en portada
                    // obtenemos los datos
                    $posts = Model\Post::getAll('home', $_SESSION['admin_node']);

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'order',
                            'posts' => $posts
                        )
                    );
                    break;
                case 'up':
                    if (!empty($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) {
                        Model\Post::up_node($id, $_SESSION['admin_node']);
                    } else {
                        Model\Post::up($id, 'home');
                    }
                    throw new Redirection('/admin/blog/reorder');
                    break;
                case 'down':
                    if (!empty($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) {
                        Model\Post::up_node($id, $_SESSION['admin_node']);
                    } else {
                        Model\Post::down($id, 'home');
                    }
                    throw new Redirection('/admin/blog/reorder');
                    break;
                case 'add_home':
                    // siguiente orden
                    if (!empty($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) {
                        $next = Model\Post::next_node($_SESSION['admin_node']);
                        $data = (object) array('post' => $id, 'node' => $_SESSION['admin_node'], 'order' => $next);
                        if (Model\Post::update_node($data, $errors)) {
                            Message::Info('Entrada colocada en la portada correctamente');
                        } else {
                            Message::Error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                        }
                    } else {
                        $next = Model\Post::next('home');
                        $post = new Model\Post(array(
                            'id' => $id,
                            'order' => $next,
                            'home' => 1
                        ));

                        if ($post->update($errors)) {
                            Message::Info('Entrada colocada en la portada correctamente');
                        } else {
                            Message::Error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                        }
                    }
                    throw new Redirection('/admin/blog/list');
                    break;
                case 'remove_home':
                    // se quita de la portada solamente
                    $ok = false;
                    if (!empty($_SESSION['admin_node']) && $_SESSION['admin_node'] != \GOTEO_NODE) {
                        $ok = Model\Post::remove_node($id, $_SESSION['admin_node']);
                    } else {
                        $ok = Model\Post::remove($id, 'home');
                    }
                    if ($ok) {
                        Message::Info('Entrada quitada de la portada correctamente');
                    } else {
                        Message::Error('No se ha podido quitar esta entrada de la portada');
                    }
                    throw new Redirection('/admin/blog/list');
                    break;

                // acciones footer
                case 'footer':
                    // lista de entradas en el footer
                    // obtenemos los datos
                    $posts = Model\Post::getAll('footer');

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'blog',
                            'file' => 'footer',
                            'posts' => $posts
                        )
                    );
                    break;
                case 'up_footer':
                    Model\Post::up($id, 'footer');
                    throw new Redirection('/admin/blog/footer');
                    break;
                case 'down_footer':
                    Model\Post::down($id, 'footer');
                    throw new Redirection('/admin/blog/footer');
                    break;
                case 'add_footer':
                    // siguiente orden
                    $next = Model\Post::next('footer');
                    $post = new Model\Post(array(
                        'id' => $id,
                        'order' => $next,
                        'footer' => 1
                    ));

                    if ($post->update($errors)) {
                        Message::Info('Entrada colocada en el footer correctamente');
                    } else {
                        Message::Error('Ha habido algun problema:<br />' . \implode('<br />', $errors));
                    }
                    throw new Redirection('/admin/blog/list');
                    break;
                case 'remove_footer':
                    // se quita del footer solamente
                    if (Model\Post::remove($id, 'footer')) {
                        Message::Info('Entrada quitada de la portada correctamente');
                    } else {
                        Message::Error('No se ha podido quitar esta entrada de la portada');
                    }
                    throw new Redirection('/admin/blog/list');
                    break;
            }

        }

    }

}
