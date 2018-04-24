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
use Goteo\Application\View;
use Goteo\Model;
use Goteo\Model\Blog\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BlogController extends \Goteo\Core\Controller {

    public function __construct() {
        // Cache & replica read activated in this controller
        \Goteo\Core\DB::cache(true);
        // \Goteo\Core\DB::replica(true);
        View::setTheme('responsive');

    }

    public function indexAction (Request $request) {

        $slider_posts=Post::getList([], true, 0, 3);
        $list_posts=Post::getList([], true, 3, 12);
        $blog_sections=Post::getListSections();

        return $this->viewResponse('blog/list', [
                    'slider_posts' => $slider_posts,
                    'list_posts'   => $list_posts,
                    'blog_sections'     => $blog_sections
                ]
        );

        /*$filters = array();
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
        }*/

        // segun eso montamos la vista

       

    }

    public function postAction($post, Request $request)
    {
        $post=Post::get($post, Lang::current());

        // Get related posts

        reset($post->tags);
        $first_key_tags=key($post->tags);

        $related_posts=Post::getList(['tag' => $first_key_tags, 'excluded' => $post->id ], true, 0, $limit = 3, false);

        return $this->viewResponse('blog/post', 
                [
                    'post' => $post,
                    'related_posts' => $related_posts,
                    'author' => $author
                ]
        );

    }

}


