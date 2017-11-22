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

use Goteo\Application\AppEvents;
use Goteo\Console\ConsoleEvents;
use Goteo\Application\Lang;

use Goteo\Model\Template;
use Goteo\Model\User;
use Goteo\Model\Mail;
use Goteo\Model\Matcher;
use Goteo\Model\Project;
use Goteo\Model\Project\Conf as ProjectConf;
use Goteo\Model\Event;

use Goteo\Application\Event\FilterMatcherProjectEvent;
use Goteo\Application\Event\FilterProjectEvent as AppFilterProjectEvent;
use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Application\Exception\DuplicatedEventException;

class MailingMatcherListener extends AbstractMatcherListener {
    /**
     * Executes the action of sending a message to the targets
     * Ensures that the sending is a unique event so no duplicates messages arrives to the user
     *
     * @param  Project $project    Project object to process
     * @param  string  $template   Message identifier (from the UsersSend class)
     * @param  array   $target     Receiver, the owner or the consultants
     * @param  string  $extra_hash Used to add some extra identification to the Event action to allow sending the same message more than once
     */
    protected function send(Project $project, Mail $mail, $admin = false) {
        $to = $mail->to;
        $template = $mail->template;
        $dest = $admin ? 'admin' : 'owner';
        try {
            $action = [$project->id, $to, $template, 'type' => $dest];
            $event = new Event($action);

        } catch(DuplicatedEventException $e) {
            $this->warning('Duplicated event', ['action' => $e->getMessage(), $project, $mail, 'event' => "$to:$template"]);
            return;
        }
        $event->fire(function() use ($project, $template, $dest, $mail) {
            $errors = [];
            if ($mail->send($errors)) {
                $this->notice("Communication sent successfully to $dest", ['type' => 'matcher_project', $project, 'email' => $mail->to, 'bcc' => $mail->bcc, 'template' => $template]);
            } else {
                $this->critical("ERROR sending communication to $dest", ['type' => 'matcher_project', $project, 'email' => $mail->to, 'bcc' => $mail->bcc, 'template' => $template, 'errors' => $errors]);
            }
        });

    }
    public function onMatcherProject(FilterMatcherProjectEvent $event) {
        $matcher = $event->getMatcher();
        $project = $event->getProject();

        // Do not execute this listener if not required by the processor
        if(!$this->hasAppListener($matcher)) return;

        $user = $project->getOwner();
        $admin = $matcher->getOwner();
        $original_lang = $lang = User::getPreferences($user)->comlang;
        $original_lang_admin = $lang_admin = User::getPreferences($admin)->comlang;

        $vars = [
            '%PROJECTNAME%' => $project->name,
            '%PROJECTSUMMARYURL%' => Lang::getUrl($lang) . 'dashboard/project/' . $project->id . '/summary',
            '%PROJECTURL%' => Lang::getUrl($lang) . 'project/' . $project->id,
            '%CALLNAME%' => $matcher->name,
            '%CALLURL%' => Lang::getUrl($lang) . 'matcher/' . $matcher->id,
            '%ADMINMAIL%' => $admin->email
        ];

        $mail = $mail_admin = $tpl = $tpl_admin = null;
        switch($matcher->getProjectStatus($project)) {
            case 'pending':
                // Send mail to owner and admin: added project to review
                $tpl = Template::MATCHER_PROJECT_ADDED;
                $mail = Mail::createFromTemplate($user->email, $user->name, $tpl, $vars, $lang);
            case 'accepted':
                // Send mail to admin: accepted project to review only if project in campaign
                if($project->inCampaign()) {
                    $tpl_admin = Template::MATCHER_PROJECT_ADDED_ADMIN;
                    $mail_admin = Mail::createFromTemplate($admin->email, $admin->name, $tpl_admin, $vars, $lang_admin);
                }
                break;
            case 'active':
                // Send mail to owner: project accepted in the Matcher
                $tpl = Template::MATCHER_PROJECT_ACTIVATED;
                $mail = Mail::createFromTemplate($user->email, $user->name, $tpl, $vars, $lang);
                break;
            case 'discarded':
                // Send mail to owner: project not accepted in the Matcher
                $tpl = Template::MATCHER_PROJECT_DISCARDED;
                $mail = Mail::createFromTemplate($user->email, $user->name, $tpl, $vars, $lang);
                break;
        }

        if($mail) {
            // if project is being watched, add bcc with the consultas
            $monitors = [];
            if (ProjectConf::isWatched($project->id)) {
                foreach ($project->getConsultants() as $id => $name) {
                    $u = User::getMini($id);
                    $monitors[] = $u->email;
                }
                $mail->bcc = $monitors;
            }
            $this->send($project, $mail);
        }

        if($mail_admin) {
            $this->send($project, $mail_admin, true);
        }
    }

    /**
     * Send mails on project publish if required
     * This is compatible for both Application and Console PROJECT_PUBLISH event
     */
    public function onProjectPublish(FilterProjectEvent $event) {
        $project = $event->getProject();
        if($matchers = Matcher::getFromProject($project, ['pending', 'accepted'])) {
            foreach($matchers as $matcher) {

                // Do not execute this listener if not required by the processor
                if($event instanceOf AppFilterProjectEvent) {
                    if(!$this->hasAppListener($matcher)) continue;
                } else {
                    if(!$this->hasConsoleListener($matcher)) continue;
                }

                $admin = $matcher->getOwner();
                $original_lang_admin = $lang_admin = User::getPreferences($admin)->comlang;

                $vars = [
                    '%PROJECTNAME%' => $project->name,
                    '%PROJECTSUMMARYURL%' => Lang::getUrl($lang_admin) . 'dashboard/project/' . $project->id . '/summary',
                    '%PROJECTURL%' => Lang::getUrl($lang_admin) . 'project/' . $project->id,
                    '%CALLNAME%' => $matcher->name,
                    '%CALLURL%' => Lang::getUrl($lang_admin) . 'matcher/' . $matcher->id,
                    '%ADMINMAIL%' => $admin->email
                ];

                $tpl_admin = Template::MATCHER_PROJECT_ADDED_ADMIN;
                $mail_admin = Mail::createFromTemplate($admin->email, $admin->name, $tpl_admin, $vars, $lang_admin);
                $this->send($project, $mail_admin, true);
            }
        }
    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::MATCHER_PROJECT => 'onMatcherProject',
            AppEvents::PROJECT_PUBLISH => 'onProjectPublish',
            ConsoleEvents::PROJECT_PUBLISH => 'onProjectPublish'
		);
	}
}
