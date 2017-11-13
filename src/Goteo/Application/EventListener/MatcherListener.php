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
use Goteo\Application\Lang;
use Goteo\Application\Config;
use Goteo\Application\Event\FilterProjectEvent;
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Model\Template;
use Goteo\Model\User;
use Goteo\Model\Mail;
use Goteo\Model\Project\Conf as ProjectConf;

use Goteo\Application\Event\FilterMatcherProjectEvent;

class MatcherListener extends AbstractListener {


    public function onMatcherProject(FilterMatcherProjectEvent $event) {
        $matcher = $event->getMatcher();
        $project = $event->getProject();
        $user = $project->getOwner();
        $original_lang = $lang = User::getPreferences($user)->comlang;

        $vars = [
            '%CALLNAME%' => $matcher->name,
            '%PROJECTURL%' => Lang::getUrl($lang) . 'dashboard/project/' . $project->id . '/summary',
            '%CALLURL%' => Lang::getUrl($lang) . 'matcher/' . $matcher->id,
        ];

        $mail = $tpl = null;
        switch($matcher->getProjectStatus($project)) {
            case 'pending':
                // Send mail to owner and admin: added project to review
                $tpl = Template::MATCHER_PROJECT_ADDED;
                $mail = Mail::createFromTemplate($user->email, $user->name, $tpl, $vars, $lang);
                break;
            case 'accepted':
                // Send mail to admin: accepted project to review
            case 'active':
                // Send mail to owner: project accepted in the Matcher
            case 'discarded':
                // Send mail to owner: project not accepted in the Matcher
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
    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::MATCHER_PROJECT => 'onMatcherProject'
		);
	}
}
