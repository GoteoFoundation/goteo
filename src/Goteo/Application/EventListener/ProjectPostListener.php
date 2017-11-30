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
use Goteo\Console\UsersSend;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;

use Goteo\Application\Event\FilterProjectPostEvent;
use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Application\Session;
use Goteo\Model\Project;
use Goteo\Model\Blog\Post as BlogPost;
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

        // si no ha encontrado otro, lanzamos la notificación a cofinanciadores
        // y el post no es demasiado viejo
        $current = ($post->date instanceOf \DateTime) ? $post->date : new \DateTime($post->date);
        if (!$log->unique_issue && $project->num_investors && (new \DateTime('-2 week')) <  $current) {
            $this->notice("Sending massive mailing for publish post", [['unique' => $log->unique_issue, 'num_investors' => $project->num_investors], $post, $project]);
            UsersSend::setURL(Config::getUrl($project->lang));
            UsersSend::toInvestors('update', $project, null, $post);
            Message::info(Text::get('dashboard-project-updates-sent-to-investors'));

        } else {
            $this->warning("NOT sending massive mailing for publish post", [['unique' => $log->unique_issue, 'num_investors' => $project->num_investors], $post, $project]);

            if($log->unique_issue) $reason = Text::get('dashboard-project-updates-sent-reason-no-unique');
            elseif(!$project->num_investors) $reason = Text::get('dashboard-project-updates-sent-reason-no-investors');
            else $reason = Text::get('dashboard-project-updates-sent-reason-too-old');
            Message::error(Text::get('dashboard-project-updates-sent-to-investors-error', $reason));
        }
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
            //Insert milestone
            $project_milestone = new ProjectMilestone();
            $project_milestone->project = $pid;
            $project_milestone->post = $post->id;
            $project_milestone->date = $post->date;
            $project_milestone->save($errors);
            $this->info("Creating milestone for publish post", [$post, $project_milestone]);
            // print_r($project_milestone);print_r($errors);die;
            $this->createFeed($post);
        }
        elseif(!$post->publish)
        {
            //Delete milestone
            $project_milestone = new ProjectMilestone();
            $project_milestone->project = $pid;
            $project_milestone->post = $post->id;
            $this->info("Deleting milestone for publish post", [$post, $project_milestone]);
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
