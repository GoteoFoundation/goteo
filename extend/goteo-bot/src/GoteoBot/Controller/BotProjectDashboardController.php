<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace GoteoBot\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Goteo\Application\Session;
use Goteo\Model\Project;
use Goteo\Library\Text;
use GoteoBot\Model\Bot\TelegramBot;

class BotProjectDashboardController extends \Goteo\Controller\Dashboard\ProjectDashboardController {
    protected $user, $admin = false;

    static function createBotSidebar(Project $project) {

        if(!$project) return;
        if(!$project instanceOf Project) $project = Project::get($project);
        if(!$project->userCanEdit(Session::getUser())) return;

        // $prefix = '/dashboard/project/' . $project->id;
        // Session::addToSidebarMenu('<i class="fa fa-bell fa-2x"></i> ' . Text::get('dashboard-bot-project-integration'), $prefix . '/integration',  'integration', 100);
    }

    public function integrationAction($pid, Request $request) {

        $project = $this->validateProject($pid);
        if($project instanceOf Response) return $project;
        
        $token = \mybase64_encode($project->id);

        $url = TelegramBot::URL . "/" . TelegramBot::getName() . "?start=" . $token;
        
        return $this->viewResponse('dashboard/project/integration',[
            'project' => $project,
            'token' => $token,
            'url' => $url
        ]);
    }

}
