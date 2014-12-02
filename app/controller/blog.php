<?php

namespace Goteo\Controller {

    use Goteo\Core\View,
        Goteo\Library\Text,
        Goteo\Library\Message,
        Goteo\Model;

    class Blog extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador blog
            \Goteo\Core\DB::cache(true);
        }

        public function index ($post = null) {

            if (!empty($post)) {
                $show = 'post';
                // -- Mensaje azul molesto para usuarios no registrados
                if (empty($_SESSION['user'])) {
                    $_SESSION['jumpto'] = '/blog/' .  $post;
                    Message::Info(Text::html('user-login-required'));
                }
            } else {
                $show = 'list';
            }

            // sacamos su blog
            $blog = Model\Blog::get(\GOTEO_NODE, 'node');

            $filters = array();
            if (isset($_GET['tag'])) {
                $tag = Model\Blog\Post\Tag::get($_GET['tag']);
                if (!empty($tag->id)) {
                    $filters['tag'] = $tag->id;
                }
            } else {
                $tag = null;
            }

            if (isset($_GET['author'])) {
                $author = Model\User::getMini($_GET['author']);
                if (!empty($author->id)) {
                    $filters['author'] = $author->id;
                }
            } else {
                $author = null;
            }

            if (!empty($filters)) {
                $blog->posts = Model\Blog\Post::getList($filters);
            }

            if (isset($post) && !isset($blog->posts[$post])) {
                // para ver entradas de novedades de proyecto
                $blog->posts[$post] = Model\Blog\Post::get($post);

                // si preview
                if (!$blog->posts[$post]->publish &&
                    ( $_GET['preview'] != $_SESSION['user']->id
                        || !isset($_GET['preview'])
                        )
                    )
                    throw new \Goteo\Core\Redirection('/blog');
            }

            // segun eso montamos la vista
            return new View(
                'blog/index.html.php',
                array(
                    'blog' => $blog,
                    'show' => $show,
                    'filters'  => $filters,
                    'post' => $post,
                    'owner' => \GOTEO_NODE
                )
             );

        }

    }

}
