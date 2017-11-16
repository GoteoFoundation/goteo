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
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

use Goteo\Application\Event\FilterProjectPostEvent;
use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Application\Session;
use Goteo\Model\Project;
use Goteo\Model\Blog\Post as BlogPost;
use Goteo\Model\User;
use Goteo\Model\Event;
use Goteo\Model\Project\ProjectMilestone;

class ProjectPostListener extends AbstractListener {

    private function createFeed(BlogPost $post) {
        $project = Project::get($post->owner_id);
        $user = Session::getUser();

        if (is_object($post->image)) {
            $image = $post->image->id;
        } elseif (!empty($post->image)) {
            $image = $post->image;
        }  else {
            $image = (!empty($project->image->id)) ? $project->image->id : 'empty';
        }

        // Evento Feed
        $log = new Feed();
        $log->setTarget($project->id)
            ->setPost($post->id)
            ->populate('feed-project-new-post',
                '/project/' . $project->id . '/updates/' . $post->id,
                new FeedBody(null, null, 'feed-project-post-published', [
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id),
                    '%BLOG%'    => new FeedBody('blog', null, 'project-menu-updates'),
                    '%USER%'    => Feed::item('user', $user->name, $user->id),
                    '%TITLE%'   => Feed::item('update', $post->title, $project->id . '/updates/' . $post->id)
                ]),
                $image)
            ->setUnique(true)
            ->doAdmin('user')

            // Public event
            ->populate($post->title,
                '/project/' . $project->id . '/updates/' . $post->id,
                new FeedBody(null, null, 'feed-new_update', [
                    '%USER%' => Feed::item('user', $user->name, $user->id),
                    '%BLOG%' => new FeedBody('blog', null, 'project-menu-updates'),
                    '%PROJECT%' => Feed::item('project', $project->name, $project->id)
                ]),
                $image)
            ->doPublic('projects');
    }

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
            $project_milestone = new ProjectMilestone();
            $project_milestone->project = $pid;
            $project_milestone->post = $post->id;
            $project_milestone->date = $post->date;
            $project_milestone->save($errors);
            // print_r($project_milestone);print_r($errors);die;
            $this->createFeed($post);
        }
        elseif(!$post->publish)
        {
            $this->info("Deleting milestone for publish post", [$post]);
            //Delete milestone
            $project_milestone = new ProjectMilestone();
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
