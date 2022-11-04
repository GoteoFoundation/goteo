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

use Goteo\Application\Currency;
use Goteo\Application\Exception\ControllerAccessDeniedException;
use Goteo\Library\Text;
use Goteo\Model\Call;
use Goteo\Model\Matcher;
use Goteo\Model\Origin;
use Goteo\Model\Project;
use Symfony\Component\HttpFoundation\Request;


class ChartsApiController extends AbstractApiController {

    public function __construct() {
        parent::__construct();
        $this->dbReplica(true);
        $this->dbCache(true);
    }


    protected function getProject($prj, $private = false) {
        if( ! $prj instanceOf Project) {
            $prj = Project::get($prj);
        }

        $is_visible = in_array($prj->status, [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED]) && !$private;

        $is_mine = $prj->userIsOwner($this->user);
        if(!$this->is_admin && !$is_mine && !$is_visible) {
            throw new ControllerAccessDeniedException();
        }
        return $prj;
    }

    protected function getCall($call, $private = false) {
        if( ! $call instanceOf Call) {
            $call = Call::get($call);
        }

        $is_visible = in_array($call->status, [Call::STATUS_OPEN, Call::STATUS_ACTIVE, Call::STATUS_COMPLETED]) && !$private;

        $is_mine = $call->owner === $this->user->id;
        if(!$this->is_admin && !$is_mine && !$is_visible) {
            throw new ControllerAccessDeniedException();
        }
        return $call;
    }

    protected function getMatcher($matcher, $private = false) {
        if( ! $matcher instanceOf Matcher) {
            $matcher = Matcher::get($matcher);
        }

        $is_mine = $matcher->owner === $this->user->id;
        if(!$this->is_admin && !$is_mine) {
            throw new ControllerAccessDeniedException();
        }
        return $matcher;
    }

    /**
     * Simple projects info data specially formatted for D3 charts
     * COSTS
     */
    public function projectCostsAction($id, Request $request) {
        $prj = $this->getProject($id);
        $mincost = (int) Currency::amount($prj->mincost);
        $maxcost = (int) Currency::amount($prj->maxcost);

        //add costs
        $costs = ['mandatory' => [], 'optional' => []];

        //Light colors
        $light_o=30;
        $light_m=40;

        foreach(Project\Cost::getAll($id) as $cost) {
            $arr = $cost->required ? 'mandatory' : 'optional';

            if($arr=='mandatory')
            {
                $light_m+=4;
                $color='hsla(303, 46%,'. $light_m.'%, 1)';
                $color_first='hsla(303, 46%, 40%, 1)';
            }
            else
            {
                $light_o-=4;
                $color='hsla(179, 76%,'. $light_m.'%, 1)';
                $color_first='hsla(179, 76%, 30%, 1)';
            }

            if(!is_array($costs[$arr][$cost->type])) $costs[$arr][$cost->type] = ['name' => Text::get('cost-type-' . $cost->type), 'size' => 0, 'color' => $color, 'children' => []];
            $costs[$arr][$cost->type]['children'][] = ['name' => $cost->cost, 'title' => \amount_format($cost->amount), 'color' => $color, 'size' => (int)$cost->amount];
            $costs[$arr][$cost->type]['size'] += (int)$cost->amount;
            $costs[$arr][$cost->type]['color'] = $color_first;
        }

        $ob = ['size' => $mincost + $maxcost, 'name' => $prj->name, 'children' => [
            ['size' => $mincost, 'name' => Text::get('project-view-metter-minimum'), 'color' =>'hsla(303, 46%, 30%, 1)', 'children' => array_values($costs['mandatory'])],
            ['size' => $maxcost, 'name' => Text::get('project-view-metter-optimum'), 'color' =>'hsla(179, 76%, 20%, 1)', 'children' => array_values($costs['optional'])],
        ]];

        return $this->jsonResponse($ob);
    }

    /**
     * Simple projects info data specially formatted for D3 charts
     * INVESTS
     */
    public function projectInvestsAction($id, Request $request) {
        $prj = $this->getProject($id);
        $mincost = (int) Currency::amount($prj->mincost);
        $maxcost = (int) Currency::amount($prj->maxcost);
        $empty = ['date' => null, 'amount' => 0, 'ideal' => 0, 'invests' => 0, 'desc' => 'Invest', 'cumulative' => 0, 'cumulative-invests' => 0, 'cumulative-amount-percent' => 0, 'cumulative-invests-percent' => 0];
        $time = strtotime($prj->published);
        $time_round1 = mktime(0,0,0,date('m', $time), date('d', $time) + (int)$prj->days_round1, date('Y', $time));
        $time_round2 = mktime(0,0,0,date('m', $time), date('d', $time) + (int)$prj->days_round1 + (int)$prj->days_round2, date('Y', $time));
        $round1 = date("Y-m-d", $time_round1);
        $round2 = date("Y-m-d", $time_round2);
        $ob = [
            // Initial point
            $prj->published => ['date' => $prj->published, 'desc' => 'Campaign start'] + $empty,
            // Ideal minimum point
            $round1 => ['date' => $round1, 'ideal' => $mincost, 'desc' => 'End Round 1'] + $empty
        ];

        // Ideal optimum point
        if(!$prj->one_round) {
            $ob[$round2] = ['date' => $round2, 'ideal' => $maxcost, 'desc' => 'End Round 2'] + $empty;
        }

        $cumulative = $cumulative_invests = 0;
        $total = $prj->getTotalInvestions();
        foreach($prj->getInvestions(0, $total) as $invest) {
            $amount = Currency::amount($invest->amount);
            if(is_array($ob[$invest->invested])) {
                $ob[$invest->invested]['amount'] += $amount;
                $ob[$invest->invested]['invests'] ++;
            } else {
                $t = strtotime($invest->invested);
                if($t <= $time_round1) {
                    $ideal = round($mincost * ($t - $time) / ($time_round1 - $time));
                }
                else {
                    $ideal = round(($maxcost - $mincost) * ($t - $time_round1) / ($time_round2 - $time_round1)) + $mincost;
                }
                $ob[$invest->invested] = ['date' => $invest->invested, 'amount' => $amount, 'ideal' => $ideal, 'invests' => 1] + $empty;
            }
            $cumulative += $amount;
            $cumulative_invests ++;
            $ob[$invest->invested]['cumulative'] = round($cumulative);
            $ob[$invest->invested]['cumulative-invests'] = round($cumulative_invests);
            $ob[$invest->invested]['cumulative-amount-percent'] = $mincost ? round(100 * $cumulative / $mincost, 2) : 0;
            $ob[$invest->invested]['cumulative-invests-percent'] = $total ? round(100 * $cumulative_invests / $total, 2) : 0;
        }
        ksort($ob);
        $ret = [];
        $last = 0;
        foreach($ob as $k => $v) {
            if(empty($v['invests'])) {
                $v['cumulative'] = $last['cumulative'];
                $v['cumulative-invests'] = $last['cumulative-invests'];
                $v['cumulative-amount-percent'] = $last['cumulative-amount-percent'];
                $v['cumulative-invests-percent'] = $last['cumulative-invests-percent'];
            }
            $ret[] = $v;
            $last = $v;
        }

        return $this->jsonResponse($ret);
    }


    /**
     * Simple projects origins data
     */
    public function originStatsAction(Request $request, $id = null, $type = 'project', $group = 'referer') {

        if($type === 'call')
            $model = $id ? $this->getCall($id, true) : new Call();
        else if ($type == 'matcher') {
            $model = $id ? $this->getMatcher($id, true) : new Matcher();
        }
        else
            $model = $id ? $this->getProject($id, true) : new Project();

        $group_by = $request->query->get('group_by');
        $filters = [
            'from' =>$request->query->get('from'),
            'to' => $request->query->get('to'),
            'call' => $request->query->get('call'),
            'matcher' => $request->query->get('matcher'),
            'channel' => $request->query->get('channel'),
            'project' => $request->query->get('project'),
            'user' => $request->query->get('user'),
            'consultant' => $request->query->get('consultant')
        ];

        if($type === 'invests') $ret = Origin::getInvestsStats($model, $group, $group_by, $filters);
        else $ret = Origin::getModelStats($model, $group, $group_by, $filters);

        $ret = array_map(function($ob) use ($group_by) {
            $label = $ob->tag ? $ob->tag : 'unknown';
            if($group_by === '  ') $label = $ob->category ? $ob->category : 'unknown';
            elseif($ob->category === 'internal') {
                $label = $ob->category . ": " . ucfirst($label);
            }

            return [
                'label' => ucfirst($label),
                'counter' => (int) $ob->counter,
                'created' => $ob->created,
                'updated' => $ob->updated
            ];
        }, $ret);

        return $this->jsonResponse($ret);
    }

    /**
     * Gets amount for a Matcher
     * @param  Matcher $mid
     * @param  string $type amount, raised, projects
     */
    public function statsMatcherAction($mid = null, $type = 'amount') {
        $matcher = Matcher::get($mid);
        if (!$matcher) {
            return $this->jsonResponse(null);
        }

        $result = [];

        if ($type == 'amount') {
            $used = $matcher->getUsedAmount();
            $available = $matcher->getTotalAmount() - $used;
            $result[] = [
                'label' => Text::get('dashboard-matcher-api-amount-used'),
                'counter' => $used
            ];
            $result[] = [
                'label' => Text::get('dashboard-matcher-api-amount-available'),
                'counter' => $available
            ];
        } else if ($type == 'raised') {
            $raised = $matcher->getCrowdAmount();
            $used = $matcher->getUsedAmount();
            $result[] = [
                'label' => Text::get('dashboard-matcher-api-raised-raised'),
                'counter' => $raised
            ];
            $result[] = [
                'label' => Text::get('dashboard-matcher-api-raised-used'),
                'counter' => (int) $used
            ];
        } else if ($type == 'projects') {
            foreach (Matcher::$statuses as $value) {
                $result[] = [
                    'label' => Text::get('dashboard-matcher-api-projects-' . $value),
                    'counter' => (int) count($matcher->getProjects($value))
                ];
            }
        }

        return $this->jsonResponse($result);
    }

    /**
     * Simple matcher's projects origins data
     */
    public function originMatcherStatsAction(Request $request, $group = 'referer') {

        $model = new Project();

        $group_by = $request->query->get('group_by');
        $filters = [
            'from' =>$request->query->get('from'),
            'to' => $request->query->get('to'),
            'call' => $request->query->get('call'),
            'matcher' => $request->query->get('matcher'),
            'channel' => $request->query->get('channel'),
            'project' => $request->query->get('project'),
            'user' => $request->query->get('user'),
            'consultant' => $request->query->get('consultant')
        ];

        $ret = Origin::getModelStats($model, $group, $group_by, $filters);

        $ret = array_map(function($ob) use ($group_by) {
            $label = $ob->tag ? $ob->tag : 'unknown';
            if($group_by === 'project_id') $label = $ob->project_id;

            return [
                'label' => ucfirst($label),
                'counter' => (int) $ob->counter,
                'created' => $ob->created,
                'updated' => $ob->updated
            ];
        }, $ret);

        return $this->jsonResponse($ret);
    }

}
