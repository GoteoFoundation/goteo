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

use Goteo\Application\Event\FilterMessageEvent;
use Goteo\Application\AppEvents;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;


class MessageListener extends AbstractListener {
	public function onMessageCreated(FilterMessageEvent $event) {
        $message = $event->getMessage();
        $project = $message->getProject();
        $user = $message->getUser();
        $type = $message->getType();
        $this->info("Message created", ['project' => $message->project, 'message_id' => $message->id, 'message' => $message->message]);

        $title = substr($message->message, 0, strpos($message->message, ':'));
        // Message created from support type
        if($type === 'project-support') {
            // Feed event
            $log = new Feed();
            $log->setTarget($project->id)
                ->populate('feed-message-new-support',
                '/admin/projects',

               new FeedBody(null, null, 'feed-message-message-published', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%TYPE%'    => new FeedBody('message', null, 'message-project-support'),
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TITLE%'    => Feed::item('update', $title, $project->id . '/participate#message' . $message->id)
                ]))
                ->doAdmin('user');

            // evento público, si el proyecto es público
            if ($project->isApproved()) {
                $log->populate($user->name,
                    '/user/profile/' . $user->id,
                    new FeedBody(null, null, 'feed-new-support', [
                        '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                        '%TITLE%' => Feed::item('update', $title, $project->id . '/participate#message' . $message->id)
                    ]),
                    $user->avatar->id)
                    ->doPublic('community');
            }
        }
        if($type === 'project-support-response') {
            $log = new Feed();
            $log->setTarget($project->id)
                ->populate('feed-message-new-project-response',
                '/admin/projects',

               new FeedBody(null, null, 'feed-message-support-response-published', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TITLE%'    => Feed::item('update', $title, $project->id . '/participate#message' . $message->id)
                ]))
                ->doAdmin('user');

            // evento público, si el proyecto es público
            $log->populate($user->name,
                '/user/profile/' . $user->id,
                new FeedBody(null, null, 'feed-message-support-response', [
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TITLE%' => Feed::item('update', $title, $project->id . '/participate#message' . $message->id)
                ]),
                $user->avatar->id)
                ->doPublic('community');
        }
        if($type === 'project-comment') {
        }
        if($type === 'project-comment-response') {
        }
    }

	public static function getSubscribedEvents() {
		return array(
			AppEvents::MESSAGE_CREATED => 'onMessageCreated',
		);
	}
}
