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
use Goteo\Application\Event\FilterProjectEvent;
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\Text;
use Goteo\Library\FeedBody;

use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Model\Project;
use Goteo\Model\Event;

//

class ProjectListener extends AbstractListener {

	private function logFeedEntry(Feed $log) {
		if ($log->unique_issue) {
			$this->warning("Duplicated feed", [$project, $log]);
		} else {
			$this->notice("Populated feed", [$project, $log]);
		}
	}

    /**
     * Executes the action of sending a message to the targets
     * Ensures that the sending is a unique event so no duplicates messages arrives to the user
     *
     * @param  Project $project    Project object to process
     * @param  string  $template   Message identifier (from the UsersSend class)
     * @param  array   $target     Receiver, the owner or the consultants
     * @param  string  $extra_hash Used to add some extra identification to the Event action to allow sending the same message more than once
     */
    private function send(Project $project, $template, $target = ['owner'], $extra_hash = '') {
        if(!is_array($target)) $target = [$target];
        foreach($target as $to) {
            if(!in_array($to, ['owner', 'consultants'])) {
                throw new \LogicException("Target [$to] not allowed");
            }
            try {
                $action = [$project->id, $to, $template];
                if($extra_hash) $action[] = $extra_hash;
                $event = new Event($action);

            } catch(DuplicatedEventException $e) {
                $this->warning('Duplicated event', ['action' => $e->getMessage(), $project, 'event' => "$to:$template"]);
                return;
            }
            $event->fire(function() use ($project, $template, $to) {
                UsersSend::setURL(Config::getUrl($project->lang));
                if('owner' === $to) $ok = UsersSend::toOwner($template, $project);
                if('consultants' === $to) $ok = UsersSend::toConsultants($template, $project);

                if($ok) $this->notice("Sent message to $to", [$project, 'event' => "$to:$template"]);
                else    $this->error("Error sending message to $to", [$project, 'event' => "$to:$template"]);
            });

        }
    }

    /**
     * Project created, send emails
     * @param  FilterProjectEvent $event
     */
    public function onProjectCreated(FilterProjectEvent $event) {
        $project = $event->getProject();
        $user = $event->getUser();
        $this->info("New project created", [$project, $user]);

        // This is not an unique event, sending manually
        // Send a mail to the creator
        $project->user = $user;
        $ok = UsersSend::toOwner('project_created', $project);
        if($ok) $this->notice("Sent message to owner", [$project, 'event' => "owner:project_created"]);
        else    $this->error("Error sending message to owner", [$project, 'event' => "owner:project_created"]);

    }

    /**
     * Manually publishes projects
     * @param  FilterProjectEvent $event
     */
    public function onProjectPublish(FilterProjectEvent $event) {
        $project = $event->getProject();
        $user = $event->getUser();
        $this->info("Manual publish of project", [$project, $user]);

        $errors = [];
        $res = $project->publish($errors);
        if ($res) {
            $this->send($project, 'tip_0', ['owner', 'consultants']);
        } else {
            $this->error('Error publishing project! '.implode("\n", $errors), [$project]);
        }
        // Admin Feed
        $log = new Feed();
        // We don't want to repeat this feed
        $log->setTarget($project->id)
            ->populate('feed-admin-project-action',
                '/admin/projects',
                new FeedBody(null, null, 'feed-admin-project-published-' . ($res ? 'ok' : 'ko'), [
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%DAYS%'    => $project->days,
                    '%ROUND%'   => $project->round
                ]
            ),
            $project->image)
            ->doAdmin('admin');

        $this->logFeedEntry($log);

        if($res) {
            $log->unique = true;
            $log->unique_issue = false;
            // Public event
            $log->title = $project->name;
            $log->url   = '/project/'.$project->id;
            $log->setBody(new FeedBody(null, null, 'feed-new_project'));
            $log->doPublic('projects');

            $this->logFeedEntry($log);
        }
    }

    /**
     * Set project ready state
     * @param  FilterProjectEvent $event
     */
    public function onProjectReady(FilterProjectEvent $event) {
        $project = $event->getProject();
        $user = $event->getUser();
        $this->info("Project to review", [$project]);

        $isDraft = $project->isDraft();
        $errors = [];
        $res = $project->ready($errors);

        // Admin Feed
        $log = new Feed();
        // We don't want to repeat this feed
        $log->setTarget($project->id)
            ->populate('feed-project-ready-sent',
                '/project/'.$project->id,
                new FeedBody(null, null, 'feed-project-ready-' . ($res ? 'ok' : 'ko'), [
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%USER%' => Feed::item('user', $project->user->name, $project->user->id)
                ]),
                $project->image)
            ->doAdmin('project');

        $this->logFeedEntry($log);

        // This is not an unique event, sending manually
        UsersSend::setURL(Config::getUrl($project->lang));

        // email a los de goteo
        if ($isDraft) {
            $sent1 = UsersSend::toConsultants('project_preform_to_review_consultant', $project);
        } else {
            $sent1 = UsersSend::toConsultants('project_to_review_consultant', $project);
        }

        // email al autor
        $sent2 = UsersSend::toOwner('project_to_review', $project);

        if($sent1) {
            Message::info(Text::get('project-review-request_mail-success'));
        }
        if($sent2) {
            Message::info(Text::get('project-review-confirm_mail-success'));
        }
        if (!$sent1 || !$sent2) {
            $errors[] = Text::get('project-review-confirm_mail-fail');
        }

        if ($errors) {
            $this->error('Error sending project to review! '.implode("\n", $errors), [$project]);
            throw new \LogicException(implode("\n", $errors));
        }

    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::PROJECT_CREATED => 'onProjectCreated',
            AppEvents::PROJECT_PUBLISH => ['onProjectPublish', 100], // high priority
            AppEvents::PROJECT_READY   => 'onProjectReady',
		);
	}
}
