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
use Goteo\Application\Exception\ControllerException;
use Goteo\Application\Config;
use Goteo\Library\Text;
use Goteo\Application\Currency;
use Goteo\Payment\Payment;
use Goteo\Core\Model;
use Goteo\Model\Project;
use Goteo\Model\Call;
use Goteo\Model\Invest;
use Goteo\Model\Image;
use Goteo\Model\Origin;
use Goteo\Util\Stats\Stats;


class AdminChartsApiController extends ChartsApiController {

    public function __construct() {
        parent::__construct();
        // Activate replica read for this controller (cache already activated in AbstractApiController)
        \Goteo\Core\DB::replica(true);
        if(!$this->user || !$this->user->hasPerm('view-any-project')) {
            throw new ControllerAccessDeniedException();
        }
    }

    /**
     * Groups data in time units (5min, 1 hour, 1 day, etc)
     * @param  Request $request [description]
     */
    public function aggregatesAction($type = 'invests', Request $request) {
        $limit = 100;

        if($type === 'invests') {
            $table = 'invest';
            $datetime = 'datetime';
            $amount = 'amount';
            $where = "WHERE invest.status IN (". implode(',', Invest::$ACTIVE_STATUSES) . ")";
        } elseif($type === 'projects') {
            $table = 'project';
            $datetime = 'published';
            $amount = 'amount';
            $where = "WHERE project.status > (" . PROJECT::STATUS_REVIEWING . ")";
        }
        $values = [];

        $ob = Model::query("SELECT
            COUNT(*) AS `total`,
            MIN(`$datetime`) AS min_date,
            MAX(`$datetime`) AS max_date
             FROM `$table` $where", $values)->fetchObject();
        $total_items = (int) $ob->total;
        $min_date = $ob->min_date;
        $max_date = $ob->max_date;

        if($from = $request->query->get('from')) {
            if(!\date_valid($from)) throw new ControllerException("Date 'from' [$from] is invalid");
            $where .= " AND `$datetime` >= :from";
            $values[':from'] = $from;
            $min_date = $from;
        }
        if($to = $request->query->get('to')) {
            if(!\date_valid($to)) throw new ControllerException("Date 'to' [$to] is invalid");
            $where .= " AND `$datetime` <= :to";
            $values[':to'] = $to;
            $max_date = $to;
        }

        $diff = (new \DateTime($min_date))->diff(new \DateTime($max_date));
        $diff_years = $diff->y;
        $diff_months = $diff_years*12 + $diff->m;
        $diff_hours = $diff->days*24 + $diff->h;
        $diff_minutes = $diff_hours*60 + $diff->i;
        $diff_seconds = $diff_minutes*60 + $diff->s;

        // print_r($diff);die;
        $granularity =  max(60, 60 * floor($diff_minutes / $limit)); // minimun granularity is 1 minute
        $div = "UNIX_TIMESTAMP(`$datetime`) DIV $granularity";

        $total = (int)Model::query("SELECT count(*) FROM (SELECT COUNT(*) FROM `$table` $where GROUP BY $div) AS sub", $values)->fetchColumn();

        $sql = "SELECT
            FROM_UNIXTIME(($div) * $granularity) AS `date`,
            SUM(`$amount`) AS `total`,
            AVG(`$amount`) AS `average`,
            COUNT(*) AS `count`
        FROM `$table`
        $where
        GROUP BY `date`
        ORDER BY `date` DESC
        LIMIT $limit";

        $items = [];
        if($query = Model::query($sql, $values)) {
            foreach($query->fetchAll(\PDO::FETCH_OBJ) as $ob) {
                $ob->total = \amount_format($ob->total, 0, true, false, false);
                $ob->count = (int) $ob->count;
                $ob->average = round($ob->average, 2);
                $items[] = $ob;
            }
        }

        return $this->jsonResponse([
            'granularity' => $granularity,
            'granularity_hours' => floor($granularity/3600),
            'granularity_days' => floor($granularity/(3600*24)),
            'limit' => $limit,
            'total' => $total,
            'total_items' => $total_items,
            'min_date' => $min_date,
            'max_date' => $max_date,
            'diff_seconds' => $diff_seconds,
            'diff_minutes' => $diff_minutes,
            'diff_hours' => $diff_hours,
            'diff_days' => $diff->days,
            'diff_months' => $diff_months,
            'diff_years' => $diff_years,
            'items' => $items
        ]);
    }


    private function timeSlots(Request $request = null) {
        $f = 'Y-m-d H:i:s';
        $slots = [
            'today' => [
                'from' => (new \DateTime('today'))->format($f),
                'to' => (new \DateTime('now'))->format($f)
            ],
            'yesterday' => [
                'from' => (new \DateTime('yesterday'))->format($f),
                'to' => (new \DateTime('today -1 second'))->format($f)
            ],
            'week' => [
                'from' => (new \DateTime('monday this week 00:00'))->format($f),
                'to' => (new \DateTime('now'))->format($f)
            ],
            'last_week' => [
                'from' => (new \DateTime('monday -2 weeks'))->format($f),
                'to' => (new \DateTime('now -1 weeks'))->format($f)
            ],
            'month' => [
                'from' => (new \DateTime('first day of this month 00:00'))->format($f),
                'to' => (new \DateTime('now'))->format($f)
            ],
            'last_month' => [
                'from' => (new \DateTime('first day of last month 00:00'))->format($f),
                'to' => (new \DateTime('now -1 month'))->format($f)
            ],
            'last_month_complete' => [
                'from' => (new \DateTime('first day of last month 00:00'))->format($f),
                'to' => (new \DateTime('first day of this month -1 second'))->format($f)
            ],
            'year' => [
                'from' => (new \DateTime('first day of january this year'))->format($f),
                'to' => (new \DateTime('now'))->format($f)
            ],
            'last_year' => [
                'from' => (new \DateTime('first day of january last year'))->format($f),
                // 'to' => (new \DateTime('first day of january this year -1 second'))->format($f)
                // Last year so far
                'to' => (new \DateTime('now -1 year'))->format($f)
            ],
            'last_year_complete' => [
                'from' => (new \DateTime('first day of january last year'))->format($f),
                'to' => (new \DateTime('first day of january this year -1 second'))->format($f)
                ]
            ];
        if($request) {
            $to = $request->query->get('to');
            $from = $request->query->get('from');
            $project_id = $request->query->get('project');
            $call_id = $request->query->get('call');
            $matcher_id = $request->query->get('matcher');
            $user_id = $request->query->get('user');

            if($from) {
                $slots['custom'] = ['from' => (new \DateTime($from))->format($f)];
                if($to) $slots['custom']['to'] = (new \DateTime($to))->format($f);
                else  $slots['custom']['to'] = (new \DateTime('now'))->format($f);
            } elseif($project_id || $call_id || $matcher_id || $user_id) {
                $slots['custom'] = ['from' => null, 'to' => null];
            }
        }
        // print_r($slots);die;
        return $slots;
    }

    /**
     * Gets totals for invests
     * @param  string $target [raised, active, comissions, fees]
     * @param  string $method raised[paypal, tpv, ..., global]  active[paypal,...], comissions[paypal, ...], fees]
     * @param  Request $request [description]
     */
    public function totalInvestsAction($target, $method = 'global', Request $request) {
        // Use the Stats class to take advantage of the Caching component
        $stats = Stats::create('api_totals'. ($project_id ? "_$project_id" : ''), Config::get('db.cache.long_time'));

        $timeslots = self::timeSlots($request);
        $totals = [];
        foreach($timeslots as $slot => $dates) {
            $filter = ['datetime_from' => $dates['from'],
                'datetime_until' => $dates['to'],
                'projects' => $request->query->get('project'),
                'calls' => $request->query->get('call'),
                'matchers' => $request->query->get('matcher'),
                'users' => $request->query->get('user'),
                ];

            if(Payment::methodExists($method)) {
                $filter['methods'] = $method;
                $methods = [$method => Payment::getMethod($method)];
            } else {
                $method = 'global';
                $methods = Payment::getMethods();
            }
            if (in_array($target,['raised', 'active', 'raw'])) {
                $filter['status'] = Invest::$RAISED_STATUSES;
                if($target === 'active') $filter['status'] = Invest::$ACTIVE_STATUSES;
                elseif($target === 'raw') $filter['status'] = Invest::$RAW_STATUSES;
                $totals[$slot] = $stats->investTotals($filter);
            } elseif($target === 'commissions') {
                // Bank Comissions
                $totals[$slot] = ['charged' => 0, 'lost' => 0 ];
                foreach($methods as $i => $m) {
                    $raised = $stats->investTotals($filter + ['methods' => $i, 'status' => Invest::$RAISED_STATUSES]);
                    $returned = $stats->investTotals($filter + ['methods' => $i, 'status' => Invest::$FAILED_STATUSES]);
                    $totals[$slot]['charged'] += $m::calculateComission($raised['invests'], $raised['amount'], $returned['invests'], $returned['amount']);
                    $totals[$slot]['lost'] -= $m::calculateComission($returned['invests'], $returned['amount'], $returned['invests'], $returned['amount']);
                }
            } elseif($target === 'fees') {
                // Platform fees
                $filter['status'] = Invest::$ACTIVE_STATUSES;
                $totals[$slot] = $stats->investFees($filter);
                // Global invoice derives bank commissions to the project   
                // TODO: move the all to a new api end point "invoice"
                // if($slot === 'all') {
                //     $invoice = $totals['total'];
                //     foreach($methods as $i => $m) {
                //         $raised = $stats->investTotals($filter + ['methods' => $i, 'status' => Invest::$RAISED_STATUSES]);
                //         $returned = $stats->investTotals($filter + ['methods' => $i, 'status' => Invest::$FAILED_STATUSES]);
                //         $invoice +=  $m::calculateComission($raised['invests'], $raised['amount'], $returned['invests'], $returned['amount']);
                //     }
                //     $totals['invoice'] = $invoice;  
                // }
            } elseif($target === 'amounts') {
                $filter['status'] = Invest::$ACTIVE_STATUSES;
                $totals[$slot] = $stats->investAmounts($filter);
            } elseif($target !== 'global') {
                throw new ControllerException("[$target] not found, try one of [raised, active, raw, commissions, fees]");
            }
        }
        $increments = ['today' => 'yesterday', 'week' => 'last_week', 'month' => 'last_month', 'year' => 'last_year'];
        foreach($totals as $slot => $parts) {
            foreach($parts as $k => $v) {
                // increments
                if(($inc = $increments[$slot]) && is_numeric($v)) {
                    $totals[$slot][$k . '_diff'] = $v - $totals[$inc][$k];
                    $totals[$slot][$k . '_gain'] = $totals[$inc][$k] ? round(100 * (($v / $totals[$inc][$k] - 1)), 2) : 0;
                }
            }
        }
        // Add some formatting
        foreach($totals as $slot => $parts) {
            foreach($parts as $k => $v) {
                if(strpos($k, '_gain') !== false)
                    $totals[$slot][$k . '_formatted'] = number_format($v, 1, Currency::get('', 'decimal'), Currency::get('', 'thousands')) . '%';
                elseif(strpos($k, 'amount') !== false || in_array($target, ['fees', 'commissions'])) 
                    $totals[$slot][$k . '_formatted'] = \amount_format($v, 2);
            }
        }
        // print_r($totals);die;
        return $this->jsonResponse([$target => [$method => $totals], 'slots' => $timeslots]);
    }


        /**
     * Gets totals for invests
     * @param  Request $request [description]
     */
    public function totalProjectsAction($part = null, Request $request) {
        // Use the Stats class to take advantage of the Caching component
        $stats = Stats::create('api_totals', Config::get('db.cache.long_time'));
        $projects= [];

        $ofilter = [];
        if($consultant = $request->query->get('consultant')) {
            $ofilter['consultant'] = $consultant;
        }
        foreach(['created', 'published', 'reviewing', 'rejected'] as $when) {
            if($part && $part !== $when) continue;
            $date_from = 'created_from';
            $date_until = 'created_until';
            $filter = $ofilter;
            if($when === 'published') {
                $filter['status'] = [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED];
                $date_from = 'published_from';
                $date_until = 'published_until';
            }
            elseif($when === 'reviewing') {
                $filter['status'] = Project::STATUS_REVIEWING;
                $date_from = 'updated_from';
                $date_until = 'updated_until';
            }
            elseif($when === 'rejected') {
                // Rejected project have updated date defined
                // $filter['status'] = [Project::STATUS_REJECTED, Project::STATUS_EDITING];
                $filter['status'] = Project::STATUS_REJECTED;
                $date_from = 'updated_from';
                $date_until = 'updated_until';
            }

            foreach(self::timeSlots() as $slot => $dates) {
                $filter[$date_from] = $dates['from'];
                $filter[$date_until] = $dates['to'];
                $projects[$when][$slot] = $stats->projectTotals($filter, 'total');
                // $projects[$when][$slot] = rand(1,100);
            }
            if($part) return $this->jsonResponse($projects[$part]);
        }

        $pending = [];
        $filter = $ofilter;
        $filter['published_from'] = (new \DateTime('today'))->format('Y-m-d');
        $filter['status'] = Project::STATUS_REVIEWING;

        foreach(Project::getList($filter, null, 0, 100) as $prj) {
            $pending[] = [
                'id' => $prj->id,
                'image' => $prj->image ? $prj->image->getLink(64, 64, true) : '',
                'name' => $prj->name,
                'publish' => $prj->published,
                'created' => $prj->created,
                'status' => $prj->getTextStatus(),
                'consultants' => $prj->getConsultants(),
            ];
        }
        $projects['pending'] = $pending;
        return $this->jsonResponse($projects);
    }
}
