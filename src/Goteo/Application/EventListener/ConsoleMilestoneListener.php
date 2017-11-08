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
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

use Goteo\Application\Event\FilterProjectPostEvent;
use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Model\Project\Reward;
use Goteo\Model\Blog;
use Goteo\Model\User;
use Goteo\Model\Event;
use Goteo\Model\Milestone;
use Goteo\Model\Project\ProjectMilestone;

class ConsoleMilestoneListener extends AbstractListener {

     /**
     * Automatically publishes projects
     * @param  FilterProjectPostEvent $event
     */
    public function onProjectPost(FilterProjectPostEvent $event) {
        $post = $event->getPost();
        $pid = $post->owner_id;
        // Get de post Milestone
        $post_milestone = ProjectMilestone::get($pid, $post->id);
        $errors = [];
        if($post->publish && !$post_milestone)
        {
            $this->info("Creating milestone for publish post", [$post]);
            //Insert milestone
            $project_milestone = new ProjectMilestone;
            $project_milestone->project = $pid;
            $project_milestone->post = $post->id;
            $project_milestone->date = $post->date;
            $project_milestone->save($errors);
            // print_r($project_milestone);print_r($errors);die;
        }
        elseif(!$post->publish)
        {
            $this->info("Deleting milestone for publish post", [$post]);
            //Delete milestone
            $project_milestone = new ProjectMilestone;
            $project_milestone->project = $pid;
            $project_milestone->post = $post->id;
            $project_milestone->removePostMilestone($errors);

        }
        if($errors) {
            $this->error("Error creating milestone for publish post", [$post, $project_milestone, 'errors' => $errors]);
        }

    }

	public static function getSubscribedEvents() {
		return array(
            AppEvents::PROJECT_POST    => 'onProjectPost'
		);
	}
}
