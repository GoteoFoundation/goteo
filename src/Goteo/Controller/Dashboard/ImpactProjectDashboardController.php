<?php

namespace Goteo\Controller\Dashboard;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Controller\DashboardController;
use Goteo\Core\Controller;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\Model\ImpactItemProjectCollectionForm;
use Goteo\Library\Forms\Model\ImpactItemProjectForm;
use Goteo\Library\Text;
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Model\ImpactData\ImpactDataItem;
use Goteo\Model\ImpactData\ImpactDataProject;
use Goteo\Model\ImpactItem\ImpactProjectItem;
use Goteo\Model\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpactProjectDashboardController extends ProjectDashboardController
{
    public function indexAction(Request $request, $pid, $footprint_id): Response
    {
        $project = $this->validateProject($pid);
        $footprint = Footprint::get($footprint_id);

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        if (!$footprint)
            return $this->redirect("/dashboard/project/$project->id");

        $impactDataProject = ImpactDataProject::getListByProjectAndFootprint($project, $footprint);

        return $this->viewResponse(
            'dashboard/project/impact/impact_by_footprint',
            [
                'impactDataProject' => $impactDataProject,
                'project' => $project
            ]
        );
    }

    public function impactItemProjectAction(Request $request, $pid, $impact_id): Response
    {
        $project = $this->validateProject($pid);

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactData = ImpactData::get($impact_id);
        $list = ImpactProjectItem::getListByProjectAndImpactData($project, $impactData);
        return $this->viewResponse(
            'dashboard/project/impact/impact_items',
            [
                'impactData' => $impactData,
                'list' => $list,
                'count' => count($list)
            ]
        );
    }

    public function impactItemEditAction(Request $request, $pid, $id = ''): Response
    {
        $project = $this->validateProject($pid);
        $impactItemProject = ImpactProjectItem::get($id);

        $processor = $this->getModelForm(ImpactItemProjectForm::class, $impactItemProject, [], [], $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            $impactData = ImpactDataItem::getByImpactItem($impactItemProject->getImpactItem())->getImpactData();
            try {
                $processor->save($form);
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/dashboard/project/$project->id/impact/$impactData->id/impact_items");
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
                return $this->redirect("/dashboard/project/$project->id/impact/$impactData->id/impact_items");
            }
        }


        return $this->viewResponse(
            'dashboard/project/impact/impact_project_item_edit',
            [
                'project' => $project,
                'form' => $form->createView()
            ]
        );

    }
}
