<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Admin;

use Goteo\Application\Config;
use Goteo\Application\Config\ConfigException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Controller\Admin\AbstractAdminController;
use Goteo\Library\Text;
use Goteo\Model\Node;
use Goteo\Model\Node\NodeProject;
use Goteo\Model\Project;
use Goteo\Util\Form\Type\MultipleTypeaheadType;
use Goteo\Util\Form\Type\SubmitType;
use Goteo\Util\Form\Type\TypeaheadType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ChannelProjectsAdminController extends AbstractAdminController
{
    protected static string $icon = '<i class="fa fa-2x fa-wrench"></i>';

    public static function getGroup(): string
    {
        return 'channels';
    }

    public static function getRoutes(): RouteCollection
    {
        try {
            $channel = Config::get('node');
        } catch (ConfigException $e) {
            $channel = '';
        }

        $routes = new RouteCollection();
        $routes->add(
            'admin-channel-projects-list',
            new Route(
                '/{cid}',
                [
                    '_controller' => __CLASS__ . "::listAction",
                    'cid' => $channel
                ]
            )
        );

        $routes->add(
            'admin-channel-projects-delete',
            new Route(
                '/{cid}/delete/{pid}',
                [
                    '_controller' => __CLASS__ . "::deleteAction"
                ]
            )
        );

        $routes->add(
            'admin-channel-projects-add',
            new Route(
                '/{cid}/add',
                [
                    '_controller' => __CLASS__ . "::addAction"
                ]
            )
        );

        return $routes;
    }

    public function listAction(Request $request, $cid): Response
    {
        $this->createControllerContext();

        try {
            $channel = Node::get($cid);
        } catch (ModelNotFoundException $e) {
            Message::error($e->getMessage());
            return $this->redirect('/admin');
        }

        $limit = $request->query->get('limit', 20);
        $page = $request->query->get('pag', 0);

        $total = NodeProject::getList(['node' => $cid], 0, 0, true);
        $list = NodeProject::getList(['node' => $cid], $page * $limit, $limit);

        $form = $this->createProjectsForm($cid);

        return $this->viewResponse('admin/channel/projects/list', [
            'channel' => $channel,
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'form' => $form->createView()
        ]);
    }

    public function addAction(Request $request, $cid): Response
    {
        $form = $this->createProjectsForm($cid);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            foreach($data['projects'] as $project) {
                if ($project)
                    $this->createChannelProject($cid, $project, $errors);
            }
        }

        if (!empty($errors))
            Message::error(implode(',', $errors));

        return $this->redirect("/admin/channelprojects/$cid");
    }

    public function deleteAction(Request $request, $cid, $pid): Response
    {
        $nodeProject = NodeProject::getList(['node' => $cid, 'project' => $pid], 0, 1);
        if (!$nodeProject) {
            Message::error(Text::get('admin-channel-projects-not-exists'));
            return $this->redirect("/admin/channelprojects/$cid");
        }

        $errors = [];
        if (current($nodeProject)->remove($errors)) {
            Message::info(Text::get('admin-remove-entry-ok'));
        } else {
            Message::info(implode(',', $errors));
        }

        return $this->redirect("/admin/channelprojects/$cid");
    }

    private function createControllerContext(): void
    {
        $channels = Node::getList();

        $this->contextVars([
            'channels' => $channels
        ]);
    }

    private function createProjectsForm(string $cid): Form
    {
        return $this->createFormBuilder()
            ->setAction("/admin/channelprojects/$cid/add")
            ->add('projects', MultipleTypeaheadType::class, [
                'label' => 'admin-channel-projects-add',
                'required' => false,
                'sources' => 'project',
                'value_field' => 'name'
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
    }

    private function createChannelProject(string $channel, string $project, array &$errors): bool
    {
        $nodeProject = new NodeProject();
        $nodeProject->node_id = $channel;
        $nodeProject->project_id = $project;

        return $nodeProject->save($errors);
    }
}
