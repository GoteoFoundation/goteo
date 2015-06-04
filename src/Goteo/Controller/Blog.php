<?php

namespace Goteo\Controller {

    use Goteo\Application\View,
        Goteo\Library\Text,
        Goteo\Application\Message,
        Goteo\Application\Session,
        Goteo\Model,
        Symfony\Component\HttpFoundation\Response,
        Symfony\Component\HttpFoundation\RedirectResponse;;

    class Blog extends \Goteo\Core\Controller {

        public function __construct() {
            //activamos la cache para todo el controlador blog
            \Goteo\Core\DB::cache(true);
        }

        public function index ($post = null) {

            if (!empty($post)) {
                $show = 'post';
                // -- Mensaje azul molesto para usuarios no registrados
                if (empty(Session::getUser())) {
                    $_SESSION['jumpto'] = '/blog/' .  $post;
                    Message::info(Text::html('user-login-required'));
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
                    ( $_GET['preview'] != Session::getUserId()
                        || !isset($_GET['preview'])
                        )
                    )
                    new RedirectResponse('/blog');
            }

            // segun eso montamos la vista

            return new Response(View::render('blog/index', array(
                        'blog' => $blog,
                        'show' => $show,
                        'filters' => $filters,
                        'post' => $post,
                        'owner' => \GOTEO_NODE
                    )
            ));

        }

    }

}
