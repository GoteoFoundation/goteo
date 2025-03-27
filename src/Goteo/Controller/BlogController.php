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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Lang;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Application\View;
use Goteo\Core\Controller;
use Goteo\Library\Text;
use Goteo\Model\Blog\Post;
use Goteo\Model\Blog\Post\Tag;
use Goteo\Model\Node\NodePost;
use Goteo\Repository\AnnouncementRepository;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller {

    private AnnouncementRepository $announcementRepository;

    public function __construct() {
        $this->dbReplica(true);
        $this->dbCache(true);
        View::setTheme('responsive');

        $this->announcementRepository = new AnnouncementRepository();
    }

    public function indexAction(Request $request, $section='', $tag='') {

        $limit = 12;
        $slider_posts = Post::getList(['section' => $section, 'tag' => $tag], true, 0, 3);

        //convert into banners
        $banners= array_map(function($el) {
                    return (object)(
                    [
                        'image' => $el->header_image,
                        'title'       => Text::get($el::getSection($el->section)),
                        'description' => $el->title,
                        'url'         => '/blog/'.$el->id
                    ]
                );

                }, $slider_posts);

        $page = $request->query->getDigits('pag', 0);
        $init = $page * $limit;

        $list_posts = Post::getList(['section' => $section, 'tag' => $tag], true, $init, $limit);
        $total = Post::getList(['section' => $section, 'tag' => $tag], true, 0, 0, true);
        $blog_sections = Post::getListSections();
        $tag = Tag::get($tag);

        $announcements = $this->announcementRepository->getActiveList();

        return $this->viewResponse('blog/list', [
            'banners' => $banners,
            'list_posts' => $list_posts,
            'blog_sections' => $blog_sections,
            'section' => $section,
            'tag' => $tag,
            'limit' => $limit,
            'total' => $total,
            'announcements' => $announcements
        ]);
    }

    public function postAction(Request $request, $slug)
    {
        // Get related posts
        $post = Post::getBySlug($slug, Lang::current());
        $blog_sections = Post::getListSections();

        if (!$post) {
            throw new ModelNotFoundException("Post [$slug] not found!");
        }

        $channelPosts = NodePost::getList(['post' => $post->id]);
        if (!empty($channelPosts)) {
            return $this->redirect("/channel/{$channelPosts[0]->node_id}/blog/{$slug}");
        }

        $user = Session::getUser();
        if (!$post->publish) {
            if($user && $user->hasPerm('admin-module-blog')) {
                Message::error(Text::get('admin-blog-not-public'));
            } else {
                throw new ModelNotFoundException("Post [$slug] not public yet!");
            }
        }

        // Redirect to project's page if not the right type of post
        if ($post->owner_type === 'project') {
            return $this->redirect("/project/{$post->owner_id}/updates/{$post->id}");
        }

        // Redirect to slug if exists
        if($post->slug && $post->slug != $slug) {
            return $this->redirect("/blog/{$post->slug}");
        }

        $tags = $post->tags;
        reset($tags);
        $first_key_tags = key($tags);

        $related_posts = Post::getList(['tag' => $first_key_tags, 'excluded' => $post->id ], true, 0, $limit = 3, false);

        $announcements = $this->announcementRepository->getActiveList();

        return $this->viewResponse('blog/post', [
            'post' => $post,
            'blog_sections'     => $blog_sections,
            'related_posts' => $related_posts,
            'author' => $author,
            'announcements' => $announcements
        ]);
    }
}
