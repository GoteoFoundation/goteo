<?php

namespace GoteoBot\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Goteo\Model\Project;
use Goteo\Application\AppEvents;
use GoteoBot\Controller\BotProjectDashboardController;

class BotControllerListener implements EventSubscriberInterface
{


    public function onController(FilterControllerEvent $event) {
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
        $project_milestone= new ProjectMilestone;
        $project_milestone->project=$project->id;
        $project_milestone->milestone_type=$type;


        try {
            $action = [$project->id, 'milestone-day-bot', $type];
            $event = new Event($action, 'milestone');

        } catch(DuplicatedEventException $e) {
            $this->warning('Duplicated event', ['action' => $e->getMessage(), $project, 'event' => "milestone:$type"]);
            return;
        }

        $event->fire(function() use ($project_milestone) {
            $project_milestone->save($errors);
        });

        $projectBot = ProjectBot::get($project->id);
        if ($projectBot) {
            if ($projectBot->platform == ProjectBot::TELEGRAM) {
                $bot = new TelegramBot();
                $bot->createBot();
                $milestone = Milestone::get($project_milestone->milestone, $project->lang);
                if ($milestone->image) {
                    $image = Image::get($milestone->image);
                    $bot->sendImage($projectBot->channel_id, $image, $milestone->description);
                } else {
                    $bot->sendMessage($projectBot->channel_id, $milestone->description);
                }
            }
        }
    }


    /**
     * Sends a reminder to the owners that they have to accomplish with the collective returns
     * @param  FilterProjectEvent $event
     */
    public function onInvestSucceeded(FilterInvestRequestEvent $event) {

        $method   = $event->getMethod();
        $response = $event->getResponse();
        $invest   = $method->getInvest();
        $project  = $invest->getProject();

        $this->info("Creating milestones on invest");

        //Milestones by percentage
        $percentage = $project->mincost ? ($project->invested / $project->mincost) * 100 : 0;

        if($percentage>=20&&$percentage<50)
            $type='20-percent-reached';
        elseif($percentage>=50&&$percentage<90)
            $type='50-percent-reached';
        elseif($percentage>=90&&$percentage<100)
            $type='90-percent-reached';
        elseif($percentage>=100&&$percentage<200)
            $type='100-percent-reached';
        elseif($percentage>=200)
            $type='200-percent-reached';

        //Milestones by amount
        if($invest->amount>=500&&$invest->amount<1000)
            $type='invest-500';
        if($invest->amount>=1000&&$invest->amount<2500)
            $type='invest-1000';
        elseif($invest->amount>=2500)
            $type='invest-2500';

        //Milestones by donors
        if($project->num_investors==2)
            $type='2-donors';
        elseif($project->num_investors==99)
            $type='99-donors';
        elseif($project->num_investors==200)
            $type='200-donors';
        elseif($project->num_investors==500)
            $type='500-donors';


        if($type)
            $this->create_milestone($project, $type);
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onController',
            AppEvents::INVEST_SUCCEEDED    => array('onInvestSucceeded', 100)
        );
    }
}

