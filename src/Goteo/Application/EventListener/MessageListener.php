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
use Goteo\Model\Template;
use Goteo\Model\Message;
use Goteo\Model\Mail;
use Goteo\Model\User;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;


class MessageListener extends AbstractListener {
    private function sendMail(Message $message, $template, array $recipients = []) {
        // send mail to owner
        $owner = $message->getProject()->getOwner();


        foreach($recipients as $user) {
            $lang = User::getPreferences($user)->comlang;
            $mail = Mail::createFromTemplate($user->email, $user->name, $template, [
                '%MESSAGE%' => $message->message,
                '%OWNERNAME%' => $owner->name,
                '%USERNAME%' => $user->name,
                '%PROJECTNAME%' => $project->name,
                '%PROJECTURL%' => SITE_URL . '/project/' . $projectData->id . '/participate#message'.$message->id,
                '%RESPONSEURL%' => SITE_URL . '/dashboard/activity#comments-' . $message->thread
               ], $lang)
            ->send($errors);
        }

        $this->notice('Message sent', [$message, 'recipients' => $recipients, 'errors' => $errors]);
    }

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

            // No mails sent
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

            // sent mail to project owner and recipients
            $recipients = $message->getParticipants();
            $recipients[$project->getOwner()->id] = $project->getOwner();
            $this->sendMail($message, Template::THREAD_OWNER, $recipients);
        }
        if($type === 'project-comment') {
            $log = new Feed();
            $log->setTarget($project->id)
                ->populate('feed-message-new-project-response',
                '/admin/projects',

               new FeedBody(null, null, 'feed-message-thread-published', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TYPE%'    => new FeedBody('message', $project->id . '/participate#message' . $message->id, 'project-menu-messages')
                ]))
                ->doAdmin('user');

            // evento público, si el proyecto es público
            $log->populate($user->name,
                '/user/profile/' . $user->id,
                new FeedBody(null, null, 'feed-messages-new-thread', [
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TYPE%'    => new FeedBody('message', $project->id . '/participate#message' . $message->id, 'project-menu-messages'),

                ]),
                $user->avatar->id)
                ->doPublic('community');

            // sent mail to project owner
            $this->sendMail($message, Template::OWNER_NEW_THREAD, [$project->getOwner()]);
        }

        if($type === 'project-comment-response') {
            $log = new Feed();
            $log->setTarget($project->id)
                ->populate('feed-message-new-project-response',
                '/admin/projects',

               new FeedBody(null, null, 'feed-message-response-published', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TITLE%'    => Feed::item('update', $title, $project->id . '/participate#message' . $message->id)
                ]))
                ->doAdmin('user');

            // evento público, si el proyecto es público
            $log->populate($user->name,
                '/user/profile/' . $user->id,
                new FeedBody(null, null, 'feed-message-response', [
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TYPE%'    => new FeedBody('message', $project->id . '/participate#message' . $message->id, 'project-menu-messages'),

                ]),
                $user->avatar->id)
                ->doPublic('community');

            // sent mail to project owner and recipients
            $recipients = $message->getParticipants();
            $recipients[$project->getOwner()->id] = $project->getOwner();
            $this->sendMail($message, Template::THREAD_OWNER, $recipients);

        }
    }

	public static function getSubscribedEvents() {
		return array(
			AppEvents::MESSAGE_CREATED => 'onMessageCreated',
		);
	}
}
