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

        // Evento Feed
        $log = new Feed();
        $log->setTarget('goteo', 'blog')
            ->setPost($post->id)
            ->populate('feed-blog-new-post',
                '/admin/blog',
                new FeedBody(null, null, 'feed-blog-post-published', [
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

        $errors = [];
        if((bool)$post->publish) {
            $this->createFeed($post);
        }

        if($errors) {
            $this->error("Error creating milestone for publish post", [$post, $project_milestone, 'errors' => $errors]);
        }

    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::BLOG_POST    => 'onBlogPost'
		);
	}
}
