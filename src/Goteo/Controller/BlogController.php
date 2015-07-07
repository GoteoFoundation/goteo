<?php

namespace Goteo\Controller;

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Application\Config;
use Goteo\Application\Session;
use Goteo\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BlogController extends \Goteo\Core\Controller {

    public function __construct() {
        //activamos la cache para todo el controlador blog
        \Goteo\Core\DB::cache(true);
    }

    public function indexAction ($post = '', Request $request) {

        if ($post) {
            $show = 'post';
            // -- Mensaje azul molesto para usuarios no registrados
            if (!Session::isLogged()) {
                Session::store('jumpto', '/blog/' .  $post);
                Message::info(Text::html('user-login-required'));
            }
        } else {
            $show = 'list';
        }

        // sacamos su blog
        $blog = Model\Blog::get(Config::get('node'), 'node');

        $filters = array();
        if ($request->query->has('tag')) {
            $tag = Model\Blog\Post\Tag::get($request->get->query('tag'));
            if ($tag->id) {
                $filters['tag'] = $tag->id;
            }
        } else {
            $tag = null;
        }

        if ($request->query->has('author')) {
            $author = Model\User::getMini($request->query->get('author'));
            if ($author->id) {
                $filters['author'] = $author->id;
            }
        } else {
            $author = null;
        }

        if ($filters) {
            $blog->posts = Model\Blog\Post::getList($filters);
        }

        if ($post && empty($blog->posts[$post])) {
            // para ver entradas de novedades de proyecto
            $blog->posts[$post] = Model\Blog\Post::get($post);

            // si preview
            if (!$blog->posts[$post]->publish &&
                 ( !$request->query->has('preview')
                  ||
                  $request->query->get('preview') != Session::getUserId()
                 )
                )
                return $this->redirect('/blog');
        }

        // segun eso montamos la vista

        return $this->viewResponse('blog/index', array(
                    'blog' => $blog,
                    'show' => $show,
                    'filters' => $filters,
                    'post' => $post,
                    'owner' => Config::get('node')
                )
        );

    }

}


