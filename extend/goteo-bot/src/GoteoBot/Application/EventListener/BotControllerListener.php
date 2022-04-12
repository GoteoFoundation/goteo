<?php

namespace GoteoBot\Application\EventListener;

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Model\Event;
use Goteo\Model\Milestone;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectMilestone;
use GoteoBot\Controller\BotProjectDashboardController;
use GoteoBot\Model\Bot\TelegramBot;
use GoteoBot\Model\ProjectBot;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BotControllerListener implements EventSubscriberInterface
{

    public function onController(ControllerEvent $event) {
        $request = $event->getRequest();
        $controller = $request->attributes->get('_controller');
        if(!is_string($controller)) return;

        if( strpos($controller, 'Goteo\Controller\Dashboard\ProjectDashboardController') !== false ||
            strpos($controller, 'Goteo\Controller\Dashboard\TranslateProjectDashboardController') !== false ||
            strpos($controller, 'Goteo\Controller\Dashboard\SettingsDashboardController::profileAction') !== false ||
            $controller === 'Goteo\Controller\ProjectExtrasController::indexAction' ||
            strpos($controller, 'Goteo\Controller\ProjectController') !== false ||
            strpos($controller, 'Goteo\Controller\ProjectExtrasController') !== false ) {

            $pid = $request->attributes->get('pid');
            if(!$pid) return;
            BotProjectDashboardController::createBotSidebar(Project::get($pid));
        }
    }

    private function create_milestone($project, $type){
        //Insert milestone
        $projectBot = ProjectBot::get($project->id);
        if (!$projectBot) return;

        $project_milestone= new ProjectMilestone();
        $project_milestone->project=$project->id;
        $project_milestone->milestone_type=$type;
        $project_milestone->source_message='bot_message';

        try {
            $action = [$project->id, 'milestone-day-bot', $type];
            $event = new Event($action, 'milestone');
        } catch(DuplicatedEventException $e) {
            return;
        }

        $event->fire(function() use ($project_milestone) {
            $project_milestone->save($errors);
        });

        foreach($projectBot as $projBot) {

            if ($projBot->platform == ProjectBot::TELEGRAM) {
                $bot = new TelegramBot();
                $bot->createBot();
                $milestone = Milestone::get($project_milestone->milestone, $project->lang);
                if ($type == "50-percent-reached") {
                    $milestone->bot_message =  str_replace('%s',SITE_URL . '/project/'. $project->id .'/poster', $milestone->bot_message);
                }
                if ($milestone->image) {
                    $image = $milestone->image;
                    if ($image->getType() == "gif") {
                        $bot->sendAnimation($projBot->channel_id, $image, $milestone->bot_message);
                    }
                    else {
                        $bot->sendImage($projBot->channel_id, $image, $milestone->bot_message);
                    }
                } else {
                    $bot->sendMessage($projBot->channel_id, $milestone->bot_message);
                }
            }
        }
    }

    /**
     * Insert milestone depending on day
     * @param  FilterProjectEvent $event
     */
    public function onProjectActive(FilterProjectEvent $event) {
        $project = $event->getProject();
        $type = 'day-'.$event->getDays();

        if ($event->getDays() == 2) {
            //Milestones by percentage
            $percentage = $project->mincost ? ($project->invested / $project->mincost) * 100 : 0;

            if ($percentage < 5) {
                $type = 'day-2-lt-5';
            } else {
                $type = 'day-2-gt-5';
            }
        }

        $this->create_milestone($project, $type);
    }

    /**
     * Sends a reminder to the owners that they have to accomplish with the collective returns
     */
    public function onInvestSucceeded(FilterInvestRequestEvent $event) {

        $method   = $event->getMethod();
        $response = $event->getResponse();
        $invest   = $method->getInvest();
        $project  = $invest->getProject();

        //Milestones by percentage
        $percentage = $project->mincost ? ($project->invested / $project->mincost) * 100 : 0;

        if($percentage>=15&&$percentage<20)
            $type='15-percent-reached';
        elseif($percentage>=20&&$percentage<40)
            $type='20-percent-reached';
        elseif($percentage>=40&&$percentage<50)
            $type='40-percent-reached';
        elseif($percentage>=50&&$percentage<70)
            $type='50-percent-reached';
        elseif($percentage>=70&&$percentage<80)
            $type='70-percent-reached';
        elseif($percentage>=80)
            $type='80-percent-reached';

        if($type)
            $this->create_milestone($project, $type);
    }

    public function onProjectPublish(FilterProjectEvent $event) {
        $project = $event->getProject();

        $type = 'on-publish';

        $this->create_milestone($project, $type);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
            AppEvents::INVEST_SUCCEEDED    => array('onInvestSucceeded', 200),
            ConsoleEvents::PROJECT_ACTIVE    => 'onProjectActive',
            ConsoleEvents::PROJECT_PUBLISH => 'onProjectPublish',
            AppEvents::PROJECT_PUBLISH => 'onProjectPublish'
        ];
    }
}
