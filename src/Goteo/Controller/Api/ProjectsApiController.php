<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Model\Project;
use Goteo\Model\Image;


class ProjectsApiController extends AbstractApiController {

    /**
     * Simple listing of projects
     * TODO: according to permissions, filter this projects
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function projectsAction(Request $request) {
        if(!$this->user) {
            throw new ControllerAccessDeniedException();
        }
        $filters = [];
        $node = null;
        $page = max((int) $request->query->get('pag'), 0);
        $status = [
            Project::STATUS_IN_CAMPAIGN,
            Project::STATUS_FUNDED,
            Project::STATUS_FULFILLED,
            Project::STATUS_UNFUNDED,
        ];

        // General search
        if($request->query->has('q')) {
            $filters['global'] = $request->query->get('q');
        }
        if(!$this->is_admin) {
            $filters['multistatus'] = implode(",", $status);
        }

        if($request->query->has('status')) {
            $s = explode(",",preg_replace('/[^0-9,]/', '',$request->query->get('status')));
            if(!$this->is_admin) {
                $s = array_intersect($status, $s);
            }
            $filters['multistatus'] = implode(",", $s);
        }

        $limit = 25;
        $offset = $page * $limit;
        $total = Project::getList($filters, $node, 0, 0, true);
        $list = [];
        foreach(Project::getList($filters, $node, $offset, $limit) as $prj) {
            foreach(['id', 'name', 'owner', 'subtitle', 'status', 'node', 'published', 'success', 'passed', 'closed', 'video', 'image', 'lang', 'currency'] as $k)
                $ob[$k] = $prj->$k;
            foreach(['amount', 'mincost', 'maxcost'] as $k)
                $ob[$k] = (int)$prj->$k;
            $list[] = $ob;
        }

        return $this->jsonResponse([
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
            ]);
    }

    /**
     * Simple projects info data
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function projectAction($id) {
        $prj = Project::getMini($id);
        if(!$this->is_admin && !in_array($prj->status, [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED])) {
            throw new ControllerAccessDeniedException();
        }
        $ob = [];
            foreach(['id', 'name', 'owner', 'subtitle', 'status', 'node', 'published', 'success', 'passed', 'closed', 'video', 'image', 'lang', 'currency'] as $k)
                $ob[$k] = $prj->$k;
            foreach(['amount', 'mincost', 'maxcost'] as $k)
                $ob[$k] = (int)$prj->$k;

        if($prj->image instanceof Image) {
            $ob['image'] = $prj->image->id;
        } else {
            $ob['image'] = $prj->image;
        }

        //add costs
        $ob['costs'] = [];
        foreach(Project\Cost::getAll($id) as $cost) {
            if(!is_array($ob['costs'][$cost->type])) $ob['costs'][$cost->type] = [];
            $ob['costs'][$cost->type][$cost->id] = ['cost' => $cost->cost, 'description' => $cost->description, 'amount' => (int)$cost->amount, 'required' => (bool)$cost->required];
        }

        return $this->jsonResponse($ob);
    }

}
