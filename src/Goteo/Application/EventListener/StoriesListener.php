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
use Goteo\Core\Event\CreateModelEvent;
use Goteo\Core\Event\UpdateModelEvent;
use Goteo\Core\Event\DeleteModelEvent;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;

use Goteo\Application\Session;

class StoriesListener extends AbstractListener {

    private function createFeed($story) {
        $user = Session::getUser();
        $image = $story->getImage();

        $image = $image && $image->id ? $image->id : 'empty';

        // Feed event
        $log = new Feed();

        if($story->getProject())
            $log->setTarget($story->getProject()->id);

        $log->populate('feed-new-story',
                '/admin/stories',
                new FeedBody(null, null, 'feed-admin-action', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%ACTION%'  => new FeedBody('relevant', null, 'feed-story-published')
                ]),
                $image)
            ->setUnique(true)
            ->doAdmin('admin');
    }

    /**
    * @param  CreateModelEvent/UpdateModelEvent $event
    */
    public function onStoryUpdated($event) {
        if(!$event instanceOf CreateModelEvent && !$event instanceOf UpdateModelEvent) return;
        if(!$event->is('stories')) return;

        $story = $event->getModel();

        if((bool)$story->active) {
            $this->createFeed($story);
        }
    }

    public function onStoryDeleted(DeleteModelEvent $event) {
        // Check if we deleted a story, otherwise do nothing
        if(!$event->is('stories')) return;

        $user = Session::getUser();
        $story = $event->getModel();

        // Feed event
        // Feed event
        $log = new Feed();
        $log->populate('feed-deleted-story',
                '/admin/stories',
                new FeedBody(null, null, 'feed-admin-action', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%ACTION%'  => new FeedBody('relevant', null, 'feed-story-deleted')
                ]),
                $image)
            ->doAdmin('admin');

    }

	public static function getSubscribedEvents() {
		return array(
            ModelEvents::CREATED => 'onStoryUpdated',
            ModelEvents::UPDATED => 'onStoryUpdated',
            ModelEvents::DELETED => 'onStoryDeleted'
		);
	}
}
