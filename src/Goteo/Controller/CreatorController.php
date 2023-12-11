<?php

namespace Goteo\Controller;

use Goteo\Application\Message;
use Goteo\Application\View;
use Goteo\Core\Exception;
use Goteo\Library\Text;
use Goteo\Model\Blog\Post;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeProject;
use Goteo\Model\Project;
use Goteo\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatorController extends BaseSymfonyController
{
    public function __construct()
    {
        View::setTheme('responsive');
        parent::__construct();
    }

    public function indexAction(Request $request, string $user_id): Response
    {

        try {
            $user = User::get($user_id);
        } catch (Exception $e) {
            Message::error($e->getMessage());
            return $this->redirect('/');
        }

        if (!$user instanceof User) {
            Message::error(Text::get('fatal-error-user'));
            return $this->redirect('/');
        }

        if (!$user->hasRole('creator'))
            return $this->redirectToRoute('user-profile', ['id' => $user->id]);

        /** @var Project $permanentProject */
        $permanentProject = current(Project::getList(['type_of_campaign' => Project\Conf::TYPE_PERMANENT, 'owner' => $user->id, 'status' => [Project::STATUS_IN_CAMPAIGN]]));
        $listOfProjects = Project::getList(['type_of_campaign' => Project\Conf::TYPE_CAMPAIGN, 'owner' => $user->id, 'status' => [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED]]);
        $posts = Post::getList(['author' => $user->id, 'show' => 'published']);
        if (empty($permanentProject))
            return $this->redirect($this->generateUrl('user-profile', ['id' => $user->id]));

        $channels = $this->getChannelsForProject($permanentProject);

        return $this->renderFoilTemplate('creator/index', [
            'user' => $user,
            'permanentProject' => $permanentProject,
            'listOfProjects' => $listOfProjects,
            'posts' => $posts,
            'channels' => $channels
        ]);
    }


    /**
     * @return Node[]
     */
    private function getChannelsForProject(Project $project): array
    {
        $channels = [];

        $nodeProjectList = NodeProject::getList([
            'project' => $project->id
        ]);

        foreach($nodeProjectList as $node) {
            $channels[] = Node::get($node->node_id);
        }

        return $channels;
    }
}
