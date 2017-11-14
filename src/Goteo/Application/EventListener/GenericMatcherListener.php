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
use Goteo\Application\Lang;

use Goteo\Model\Template;
use Goteo\Model\User;
use Goteo\Model\Mail;
use Goteo\Model\Project\Conf as ProjectConf;

use Goteo\Application\Event\FilterMatcherProjectEvent;

class GenericMatcherListener extends AbstractMatcherListener {

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
            '%PROJECTURL%' => Lang::getUrl($lang) . 'dashboard/project/' . $project->id,
            '%CALLNAME%' => $matcher->name,
            '%CALLURL%' => Lang::getUrl($lang) . 'matcher/' . $matcher->id,
            '%ADMINMAIL%' => $matcher->getOwner()->email
        ];

        $mail = $mail_admin = $tpl = $tpl_admin = null;
        switch($matcher->getProjectStatus($project)) {
            case 'pending':
                // Send mail to owner and admin: added project to review
                $tpl = Template::MATCHER_PROJECT_ADDED;
                $mail = Mail::createFromTemplate($user->email, $user->name, $tpl, $vars, $lang);
                $tpl_admin = Template::MATCHER_PROJECT_ADDED_ADMIN;
                $mail_admin = Mail::createFromTemplate($admin->email, $admin->name, $tpl_admin, $vars, $lang_admin);
                break;
            case 'accepted':
                // Send mail to admin: accepted project to review
                // TODO: maybe in the future
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

            $errors = [];
            if ($mail->send($errors)) {
                $this->notice("Communication sent successfully to owner", ['type' => 'matcher_project', $project, 'email' => $user->email, 'bcc' => $monitors, 'template' => $tpl]);
            } else {
                $this->critical("ERROR sending communication to owner", ['type' => 'matcher_project', $project, 'email' => $user->email, 'bcc' => $monitors, 'template' => $tpl, 'errors' => $errors]);
            }
        }

        if($mail_admin) {
            $errors = [];
            if ($mail_admin->send($errors)) {
                $this->notice("Communication sent successfully to admin", ['type' => 'matcher_project', $project, 'email' => $admin->email, 'template' => $tpl_admin]);
            } else {
                $this->critical("ERROR sending communication to admin", ['type' => 'matcher_project', $project, 'email' => $admin->email, 'template' => $tpl_admin, 'errors' => $errors]);
            }
        }
    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::MATCHER_PROJECT => 'onMatcherProject'
		);
	}
}
