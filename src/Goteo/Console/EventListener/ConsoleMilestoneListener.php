<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Model\Project\Reward;
use Goteo\Model\Blog;
use Goteo\Model\User;
use Goteo\Model\Event;
use Goteo\Model\Milestone;
use Goteo\Model\Project\ProjectMilestone;

class ConsoleMilestoneListener extends AbstractListener {

    private function create_milestone($project, $type){
        //Insert milestone
        $project_milestone= new ProjectMilestone;
        $project_milestone->project=$project->id;
        $project_milestone->milestone_type=$type;


        try {
            $action = [$project->id, 'milestone-day', $type];
            $event = new Event($action, 'milestone');

            } catch(DuplicatedEventException $e) {
                $this->warning('Duplicated event', ['action' => $e->getMessage(), $project, 'event' => "milestone:$type"]);
                return;
            }

        $event->fire(function() use ($project_milestone) {
            $project_milestone->save($errors);
        });
    }

    /**
    * Sets the milestone for published projects
    * @param  FilterProjectEvent $event
    */
    public function onProjectPublish(FilterProjectEvent $event) {
        $project = $event->getProject();
        $type = 'on-publish';

        $this->info("Creating milestone for publish project");
        $this->create_milestone($project, $type);
    }


    /**
     * Insert milestone depending on day
     * @param  FilterProjectEvent $event
     */
    public function onProjectActive(FilterProjectEvent $event) {
        $project = $event->getProject();
        $type = 'day-'.$event->getDays();

        $this->info("Creating milestones depending on day");
        $this->create_milestone($project, $type);

    }

    /**
     * Insert milestone depending on reward completed
     * @param  FilterProjectEvent $event
     */
    public function onProjectWatch(FilterProjectEvent $event) {
        $project = $event->getProject();
        $this->info("Creating milestones if fulfilled");

        if(Reward::areFulfilled($project->id))
            $type='rewards-fullfilled';

        if(Reward::areFulfilled($project->id, 'social'))
            $type='social-rewards-fullfilled';

        if($type)
            $this->create_milestone($project, $type);

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

	public static function getSubscribedEvents() {
		return array(
            ConsoleEvents::PROJECT_PUBLISH    => 'onProjectPublish',
            ConsoleEvents::PROJECT_ACTIVE    => 'onProjectActive',
            ConsoleEvents::PROJECT_WATCH    => 'onProjectWatch',
			AppEvents::INVEST_SUCCEEDED    => array('onInvestSucceeded', 100)
		);
	}
}
