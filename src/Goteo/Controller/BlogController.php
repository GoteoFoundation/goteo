<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller;

use Goteo\Library\Text;
use Goteo\Application\Message;
use Goteo\Application\Lang;
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
        if(!$blog) {
            Message::error("No blogs for [" . Config::get('node') ."]!");
            return $this->redirect('/blog');
        }
        // print_r($blog);die;
        $filters = array();
        if ($request->query->has('tag')) {
            $tag = Model\Blog\Post\Tag::get($request->query->get('tag'));
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
            $blog->posts[$post] = Model\Blog\Post::get($post, Lang::current());

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


