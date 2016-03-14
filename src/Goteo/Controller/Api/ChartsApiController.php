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

use Goteo\Application\Exception\ControllerAccessDeniedException;

use Goteo\Library\Text;
use Goteo\Library\Currency;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\Image;


class ChartsApiController extends AbstractApiController {

    private function getProject($id) {
        $prj = Project::get($id);
        if(!$this->is_admin && !in_array($prj->status, [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED])) {
            throw new ControllerAccessDeniedException();
        }
        return $prj;
    }

    /**
     * Simple projects info data specially formatted for D3 charts
     * COSTS
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function projectCostsAction($id) {
        $prj = $this->getProject($id);
        $mincost = (int) Currency::amount($prj->mincost);
        $maxcost = (int) Currency::amount($prj->maxcost);

        //add costs
        $costs = ['mandatory' => [], 'optional' => []];
        foreach(Project\Cost::getAll($id) as $cost) {
            $arr = $cost->required ? 'mandatory' : 'optional';
            if(!is_array($costs[$arr][$cost->type])) $costs[$arr][$cost->type] = ['name' => Text::get('cost-type-' . $cost->type), 'size' => 0, 'children' => []];
            $costs[$arr][$cost->type]['children'][] = ['name' => $cost->cost, 'title' => \amount_format($cost->amount), 'size' => (int)$cost->amount];
            $costs[$arr][$cost->type]['size'] += (int)$cost->amount;
        }
        // $ob['children'] = array_values($costs);
        $ob = ['size' => $mincost + $maxcost, 'name' => $prj->name, 'children' => [
            ['size' => $mincost, 'name' => Text::get('project-view-metter-minimum'), 'children' => array_values($costs['mandatory'])],
            ['size' => $maxcost, 'name' => Text::get('project-view-metter-optimum'), 'children' => array_values($costs['optional'])],
        ]];

        return $this->jsonResponse($ob);
    }

    /**
     * Simple projects info data specially formatted for D3 charts
     * INVESTS
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function projectInvestsAction($id) {
        $prj = $this->getProject($id);
        $mincost = (int) Currency::amount($prj->mincost);
        $maxcost = (int) Currency::amount($prj->maxcost);
        // die("[ $mincost $maxcost ]");
        // print_r($prj);die;
        $empty = ['date' => null, 'amount' => 0, 'ideal' => 0, 'invests' => 0, 'desc' => 'Invest', 'cumulative' => 0, 'cumulative-invests' => 0, 'cumulative-amount-percent' => 0, 'cumulative-invests-percent' => 0];
        // print_r($prj);die;
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
            $ob[$invest->invested]['cumulative-amount-percent'] = round(100 * $cumulative / $mincost, 2);
            $ob[$invest->invested]['cumulative-invests-percent'] = round(100 * $cumulative_invests / $total, 2);
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
}
