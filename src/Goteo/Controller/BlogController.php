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
use Goteo\Model\Blog\Post\Tag;
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

    public function indexAction ($section='', $tag='', Request $request) {

        $limit=12;
        $slider_posts=Post::getList(['section' => $section, 'tag' => $tag], true, 0, 3);
        $init= $request->query->get('pag') ? $request->query->get('pag')*$limit : 0;

        $list_posts=Post::getList(['section' => $section, 'tag' => $tag], true, $init, $limit);
        $total=Post::getList(['section' => $section, 'tag' => $tag], true, 0, 0, true);
        $blog_sections=Post::getListSections();
        $tag=Tag::get($tag);


        return $this->viewResponse('blog/list', [
                    'slider_posts' => $slider_posts,
                    'list_posts'   => $list_posts,
                    'blog_sections'     => $blog_sections,
                    'section'           => $section,
                    'tag'               => $tag,
                    'limit'             => $limit,
                    'total'             => $total
                ]
        );
    }

    public function postAction($post, Request $request)
    {
        // Get related posts
        $post=Post::get($post, Lang::current());

        // Redirect to project's page if not the right type of post
        if($post->owner_type === 'project') {
            return $this->redirect("/project/{$post->owner_id}/updates/{$post->id}");
        }
        $tags=$post->tags;
        reset($tags);
        $first_key_tags=key($tags);

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


