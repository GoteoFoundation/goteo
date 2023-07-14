<?php

namespace Goteo\Controller\Dashboard;

use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Application\Exception\ModelException;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Application\Message;
use Goteo\Application\Session;
use Goteo\Controller\DashboardController;
use Goteo\Core\Controller;
use Goteo\Library\Forms\FormModelException;
use Goteo\Library\Forms\Model\ImpactDataProjectForm;
use Goteo\Library\Forms\Model\ImpactItemProjectCollectionForm;
use Goteo\Library\Forms\Model\ImpactItemProjectCostForm;
use Goteo\Library\Forms\Model\ImpactItemProjectForm;
use Goteo\Library\Text;
use Goteo\Model\Footprint;
use Goteo\Model\ImpactData;
use Goteo\Model\ImpactData\ImpactDataItem;
use Goteo\Model\ImpactData\ImpactDataProject;
use Goteo\Model\ImpactItem\ImpactProjectItem;
use Goteo\Model\ImpactItem\ImpactProjectItemCost;
use Goteo\Model\Project;
use Goteo\Model\Project\Cost;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImpactProjectDashboardController extends ProjectDashboardController
{

    public function indexAction(Request $request, $pid) {
        $project = $this->validateProject($pid, "impact_data_list");

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactDataProject = ImpactDataProject::getListByProject($project);

        return $this->viewResponse(
            'dashboard/project/impact/list',
            [
                'impactDataProject' => $impactDataProject,
                'project' => $project
            ]
        );
    }

    public function listByFootprintAction(Request $request, $pid, $footprint_id = null) {
        $project = $this->validateProject($pid, "footprint_$footprint_id");
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
                'footprint' => $footprint,
                'impactDataProject' => $impactDataProject,
                'project' => $project
            ]
        );
    }

    public function addImpactDataProject(Request $request, $pid, $footprint_id = null): Response
    {
        $section = $footprint_id ? "footprint_$footprint_id" : "impact_data_list";
        $project = $this->validateProject($pid, $section);
        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $options = [];

        if ($footprint_id) {
            $footprint = Footprint::get($footprint_id);
            if (!$footprint)
                return $this->redirect("/dashboard/project/$project->id");
            $options['footprint'] = $footprint;
        }

        $impactDataProject = new ImpactDataProject();
        $impactDataProject->setProject($project);
        $processor = $this->getModelForm(ImpactDataProjectForm::class, $impactDataProject, [], $options, $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-add-entry-ok'));
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
            }

            if ($footprint) {
                return $this->redirect("/dashboard/project/$project->id/impact/footprint/$footprint->id/impact_data");
            }

            return $this->redirect("/dashboard/project/$project->id/impact");
        }


        return $this->viewResponse(
            'dashboard/project/impact/add',
            [
                'project' => $project,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @throws ModelNotFoundException
     * @throws ControllerAccessDeniedException
     */
    public function impactItemProjectAction(Request $request, $pid, $impact_data_id): Response
    {
        $project = $this->validateProject($pid);

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactData = ImpactData::get($impact_data_id);
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

    public function addOrEditImpactItemAction(Request $request, $pid, $impact_data_id, $id = null): Response
    {
        $project = $this->validateProject($pid);
        if ($id) {
            $impactItemProject = ImpactProjectItem::get($id);
        } else {
            $impactItemProject = new ImpactProjectItem();
            $impactItemProject->setProject($project);
        }
        $impactData = ImpactData::get($impact_data_id);

        if (!$project instanceof Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $options = [
            'impactData' => $impactData
        ];

        $processor = $this->getModelForm(ImpactItemProjectForm::class, $impactItemProject, [], $options, $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/dashboard/project/$project->id/impact/impact_data/$impactData->id/impact_items");
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
                return $this->redirect("/dashboard/project/$project->id/impact/impact_data/$impactData->id/impact_items");
            }
        }

        return $this->viewResponse(
            'dashboard/project/impact/impact_project_item_edit',
            [
                'project' => $project,
                'impactData' => $impactData,
                'form' => $form->createView()
            ]
        );
    }
    public function impactItemProjectDeleteAction(Request $request, $pid, $id): Response
    {
        $project = $this->validateProject($pid);
        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactItemProject = ImpactProjectItem::get($id);
        if (!$impactItemProject)
            return $this->redirect("/dashboard/project/$project->id");

        try {
            $impactItemProject->dbDelete();
            Message::info(Text::get('admin-remove-entry-ok'));
        } catch (\PDOException $e) {
            Message::error($e->getMessage());
        }

        return $this->redirect();
    }

    public function impactItemProjectCostsAction(Request $request, $pid, $impact_data_id, $id): Response
    {
        $project = $this->validateProject($pid);

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactProjectItem = ImpactProjectItem::get($id);
        $impactData = ImpactData::get($impact_data_id);

        $list = ImpactProjectItemCost::getListByImpactProjectItem($impactProjectItem);
        return $this->viewResponse(
            'dashboard/project/impact/impact_items_cost_list',
            [
                'impactData' => $impactData,
                'impactProjectItem' => $impactProjectItem,
                'list' => $list,
                'count' => count($list)
            ]
        );

    }

    public function addImpactItemProjectCostsAction(Request $request, $pid, $impact_data_id, $id): Response
    {
        $project = $this->validateProject($pid);

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactProjectItem = ImpactProjectItem::get($id);
        $impactData = ImpactData::get($impact_data_id);
        $impactItem = $impactProjectItem->getImpactItem();

        $impactProjectItemCost = new ImpactProjectItemCost();
        $impactProjectItemCost->setImpactProjectItem($impactProjectItem);

        $processor = $this->getModelForm(ImpactItemProjectCostForm::class, $impactProjectItemCost, [], [], $request);
        $processor->createForm();
        $form = $processor->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod(Request::METHOD_POST)) {
            try {
                $processor->save($form);
                Message::info(Text::get('admin-' . ($id ? 'edit' : 'add') . '-entry-ok'));
                return $this->redirect("/dashboard/project/$project->id/impact/impact_data/$impactData->id/impact_item/{$impactProjectItem->getId()}/costs");
            } catch (FormModelException $e) {
                Message::error($e->getMessage());
                return $this->redirect("/dashboard/project/$project->id/impact/impact_data/$impactData->id/impact_item/{$impactProjectItem->getId()}/costs");
            }
        }

        return $this->viewResponse(
            'dashboard/project/impact/impact_items_cost_add',
            [
                'impactData' => $impactData,
                'impactProjectItem' => $impactProjectItem,
                'form' => $form->createView()
            ]
        );

    }

    public function removeImpactItemProjectCostAction(Request $request, $pid, $impact_project_item_id, $cost_id): Response
    {
        $project = $this->validateProject($pid);

        if(!$project instanceOf Project || !$project->userCanEdit(Session::getUser())) {
            throw new ControllerAccessDeniedException(Text::get('user-login-required-access'));
        }

        $impactProjectItem = ImpactProjectItem::get($impact_project_item_id);
        $cost = Cost::get($cost_id);
        $impactProjectItemCost = ImpactProjectItemCost::getByImpactProjectItemAndCost($impactProjectItem, $cost);

        try {
            $impactProjectItemCost->dbDelete();
            Message::info(Text::get('admin-remove-entry-ok'));
        } catch (ModelException $e) {
            Message::error($e->getMessage());
        }

        $route = $request->headers->get('referer');
        return $this->redirect($route);

    }
}
