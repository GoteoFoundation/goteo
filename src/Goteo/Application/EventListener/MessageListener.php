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
use Goteo\Application\Exception\MailException;
use Goteo\Application\AppEvents;
use Goteo\Application\Config;
use Goteo\Model\Template;
use Goteo\Model\Message;
use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;
use Goteo\Model\User;
use Goteo\Library\Text;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;


class MessageListener extends AbstractListener {

    private function sendMail(Message $message, $template, array $recipients = [], $delayed = false) {
        // send mail to owner
        $project = $message->getProject();
        $owner = $project->getOwner();

        if($delayed) {
            // Send as a newsletter
            $mail = Mail::createFromTemplate('any', '', $template, [
                '%MESSAGE%' => $message->getHtml(),
                '%OWNERNAME%' => $owner->name,
                '%PROJECTNAME%' => $project->name,
                '%PROJECTURL%' => SITE_URL . '/project/' . $project->id . '/participate#message'.$message->id,
                '%RESPONSEURL%' => SITE_URL . '/dashboard/activity#comments-' . $message->thread
               ])
                ->setSubject($message->getSubject())
                ->setMessage($message);

            if ( ! $mail->save($errors) ) { //persists in database
                throw new MailException(implode("\n",$errors));
            }

            // create the sender cue
            $sender = new Sender([
                'mail' => $mail->id,
                'reply' => $owner->email,
                'reply_name' => $project->name . ' ' . Text::get('regular-from') . ' ' . Config::get('mail.transport.name')
            ]);
            if ( ! $sender->save($errors) ) { //persists in database
                throw new MailException(implode("\n",$errors));
            }

            $sender->addSubscribers($recipients);
            $sender->setActive(true);
            $this->notice('Message set as massive', [$message, 'recipients' => $recipients, 'errors' => $errors]);
            return;
        }

        if(!$recipients) {
            throw new MailException(Text::get('dashboard-message-donors-error'));
        }
        $errors = [];
        foreach($recipients as $user) {
            $lang = User::getPreferences($user)->comlang;
            $mail = Mail::createFromTemplate($user->email, $user->name, $template, [
                '%MESSAGE%' => $message->getHtml(),
                '%OWNERNAME%' => $owner->name,
                '%USERNAME%' => $user->name,
                '%PROJECTNAME%' => $project->name,
                '%PROJECTURL%' => SITE_URL . '/project/' . $project->id . '/participate#message'.$message->id,
                '%RESPONSEURL%' => SITE_URL . '/dashboard/activity#comments-' . $message->thread
               ], $lang)
            // Either add a reply-to or put a link to respond in the platform
            // ->setReply($owner->email, $owner->name)
            ->setSubject($message->getSubject())
            ->setMessage($message)
            ->send($errors);

            if($errors) {
                throw new MailException(implode("\n", $errors));
            }
        }

        $this->notice('Message sent', [$message, 'recipients' => $recipients, 'errors' => $errors]);
    }

	public function onMessageCreated(FilterMessageEvent $event) {
        $message = $event->getMessage();
        $project = $message->getProject();
        $user = $message->getUser();
        $type = $message->getType();
        $this->notice("Message created", ['project' => $message->project, 'message_id' => $message->id, 'type' => $type, 'message' => $message->message]);

        $title = $message->getSubject();
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
        elseif($type === 'project-support-response') {
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
        elseif($type === 'project-comment') {
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
        elseif($type === 'project-comment-response') {
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
        elseif($type === 'project-private' || $type === 'project-private-response') {
            $log = new Feed();
            $log->setTarget($project->id)
                ->populate('feed-message-new-project-response',
                '/admin/projects',

               new FeedBody(null, null, 'feed-message-thread-published', [
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%TYPE%'    => new FeedBody('message', '/dashboard/' . $project->id . '/invests#message' . $message->id, 'project-menu-messages')
                ]))
                ->doAdmin('user');

            // sent mail to recipients
            $this->sendMail($message, Template::MESSAGE_DONORS, $message->getRecipients(), $event->getDelayed());
        }
    }

	public static function getSubscribedEvents() {
		return array(
			AppEvents::MESSAGE_CREATED => 'onMessageCreated',
		);
	}
}
