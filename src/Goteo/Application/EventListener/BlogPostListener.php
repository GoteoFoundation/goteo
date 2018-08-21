<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Application\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;
use Goteo\Application\Message;
use Goteo\Core\ModelEvents;
use Goteo\Core\Event\DeleteModelEvent;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;

use Goteo\Application\Event\FilterBlogPostEvent;
use Goteo\Application\Session;

class BlogPostListener extends AbstractListener {

    private function createFeed($post) {
        $user = Session::getUser();

        if (is_object($post->image)) {
            $image = $post->image->id;
        }  else {
            $image = $post->image ? $post->image : 'empty';
        }

        // Feed event
        $log = new Feed();
        $log->setTarget('goteo', 'blog')
            ->setPost($post->id)
            ->populate('feed-blog-new-post',
                '/admin/blog',
                new FeedBody(null, null, 'feed-blog-post-action', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%ACTION%'  => new FeedBody('relevant', null, 'admin-title-publish'),
                    '%TITLE%'   => Feed::item('blog', $post->title, $post->getSlug())
                ]),
                $image)
            ->setUnique(true)
            ->doAdmin('admin');

        $log->unique = true; // not to be repeated
        // Public event
        $log->populate($post->title,
                '/blog/' . $post->getSlug(),
                Text::recorta(strip_tags($post->text), 250),
                $image)
            ->doPublic('goteo');

    }

    /**
    * @param  FilterBlogPostEvent $event
    */
    public function onBlogPost(FilterBlogPostEvent $event) {
        $post = $event->getPost();
        $pid = $post->owner_id;

        if((bool)$post->publish) {
            $this->createFeed($post);
        }
    }

    public function onBlogDeleted(DeleteModelEvent $event) {
        // Check if we deleted a post, otherwise do nothing
        if(!$event->is('post')) return;

        $user = Session::getUser();
        $post = $event->getModel();

        // Feed event
        $log = new Feed();
        $log->setTarget('goteo', 'blog')
            ->populate('feed-blog-post-deleted',
                '/admin/blog',
                new FeedBody(null, null, 'feed-blog-post-action', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%ACTION%'  => new FeedBody('relevant', null, 'admin-title-deleted'),
                    '%TITLE%'   => Feed::item('blog', $post->title, $post->getSlug())
                ]))
            ->doAdmin('admin');

    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::BLOG_POST    => 'onBlogPost',
            ModelEvents::DELETED => 'onBlogDeleted'
		);
	}
}
