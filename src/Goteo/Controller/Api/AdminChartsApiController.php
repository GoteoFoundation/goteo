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
        if(!$this->user || !$this->user->hasPerm('view-any-project')) {
            throw new ControllerAccessDeniedException();
        }
    }


    protected function getAggregatesSQLFilter($type, Request $request) {
        $values = $filter = [];
        $project_key = $type === 'invests' ? 'invest.project' : 'project.id';
        $user_key = $type === 'invests' ? 'invest.user' : 'project.owner';
        if($request->query->has('call')) {
            $filter[] = "$project_key IN (SELECT project FROM call_project a WHERE a.call=:call)";
            $values[':call'] = $request->query->get('call');
        }
        if($request->query->has('matcher')) {
            $filter[] = "$project_key IN (SELECT project_id FROM matcher_project b WHERE b.matcher_id=:matcher)";
            $values[':matcher'] = $request->query->get('matcher');
        }
        if($request->query->has('channel')) {
            if($type === 'invests')
                $filter[] = "invest.project IN (SELECT id FROM project c WHERE c.node=:channel)";
            else
                $filter[] = "project.node=:channel";
            $values[':channel'] = $request->query->get('channel');
        }
        if($request->query->has('project')) {
            $filter[] = "$project_key=:project";
            $values[':project'] = $request->query->get('project');
        }
        if($request->query->has('user')) {
            $filter[] = "$user_key=:user";
            $values[':user'] = $request->query->get('user');
        }
        if($request->query->has('consultant')) {
            $filter[] = "$project_key IN (SELECT project FROM user_project d WHERE d.user=:consultant)";
            $values[':consultant'] = $request->query->get('consultant');
        }
        return [$filter, $values];
    }

    /**
     * Groups data in time units (5min, 1 hour, 1 day, etc)
     * @param  Request $request [description]
     */
    public function aggregatesAction($type = 'invests', Request $request) {
        $limit = 100;

        // Get the minimum,max and total items
        list($prefilter, $prevalues) = self::getAggregatesSQLFilter('invests', $request);
        $sql = "SELECT COUNT(*) AS `total`,
                MIN(`datetime`) AS min_date,
                MAX(`datetime`) AS max_date
                FROM `invest`" . ($prefilter ? ' WHERE '.implode(' AND ', $prefilter) : '');
        // die(\sqldbg($sql, $prevalues));
        $ob = Model::query($sql, $prevalues)->fetchObject();
        $total_items = (int) $ob->total;
        $min_date = $ob->min_date;
        // $max_date = null;
        $max_date = $ob->max_date;

        if($type === 'invests') {
            $table = 'invest';
            $datetime = '`datetime`';
            $amount = 'amount';
            $where = "WHERE invest.status IN (". implode(',', Invest::$ACTIVE_STATUSES) . ")";
        } elseif($type === 'projects') {
            $table = 'project';
            $datetime = 'COALESCE(`passed`,`success`,`published`)';
            $amount = 'amount';
            $where = "WHERE project.status > " . PROJECT::STATUS_REVIEWING . "";
        }
        list($filter, $values) = self::getAggregatesSQLFilter($type, $request);
        if($filter) $where .= " AND " . implode(' AND ', $filter);


        if($from = $request->query->get('from')) {
            if(!\date_valid($from)) throw new ControllerException("Date 'from' [$from] is invalid");
            $where .= " AND $datetime >= :from";
            $values[':from'] = $from;
            $min_date = $from;
        }
        if($to = $request->query->get('to')) {
            if(!\date_valid($to)) throw new ControllerException("Date 'to' [$to] is invalid");
            $where .= " AND $datetime <= :to";
            $values[':to'] = $to;
            $max_date = $to;
        }

        $diff = (new \DateTime($min_date))->diff(new \DateTime($max_date));
        $diff_years = $diff->y;
        $diff_months = $diff_years*12 + $diff->m;
        $diff_hours = $diff->days*24 + $diff->h;
        $diff_minutes = $diff_hours*60 + $diff->i;
        $diff_seconds = $diff_minutes*60 + $diff->s;

        $granularity =  max(60, 60 * floor($diff_minutes / $limit)); // minimun granularity is 1 minute
        // print_r($diff);die("$diff_minutes|$granularity");
        $div = "UNIX_TIMESTAMP($datetime) DIV $granularity";

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

        // die(\sqldbg($sql, $values));
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


    private function timeSlots($slot = '', Request $request = null) {
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
            'last_week_complete' => [
                'from' => (new \DateTime('monday -2 weeks'))->format($f),
                'to' => (new \DateTime('monday -1 weeks -1 second'))->format($f)
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
        if($slot) {
            $varis = explode(',', $slot);
            if(array_diff($varis, $slots)) {
                $slots = array_filter($slots, function ($k) use ($varis) {
                    $t = ['today', 'yesterday'];
                    foreach($varis as $slot) {
                        if(in_array($k, $t) && in_array($slot, $t)) return true;
                        if(strpos($k, $slot) !== false) return true;
                    }
                    return false;
                }, ARRAY_FILTER_USE_KEY);
            } elseif($slot !== 'custom') {
                throw new ControllerException("Slot [$slot] not found, try one of [today, yesterday, week, month, year]");
            }
        }
        // Add custom slot if searching
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
            // Return only the custom slot if no period specified
            if(!$slot || $slot === 'custom') {
                return ['custom' => $slots['custom']];
            }
        }
        // print_r($slots);die;
        return $slots;
    }

    /**
     * Gets totals for invests
     * @param  string $target [raised, active, refunded, comissions, fees]
     * @param  string $method raised[paypal, tpv, ..., global]  active[paypal,...], comissions[paypal, ...], fees]
     * @param  Request $request [description]
     */
    public function totalInvestsAction($target, $method = 'global', $slot = '', Request $request) {
        // Use the Stats class to take advantage of the Caching component
        $stats = Stats::create('api_totals'. ($project_id ? "_$project_id" : ''), 30);

        $timeslots = self::timeSlots($slot, $request);
        $totals = [];
        foreach($timeslots as $slot => $dates) {
            $filter = ['datetime_from' => $dates['from'],
                'datetime_until' => $dates['to'],
                'projects' => $request->query->get('project'),
                'calls' => $request->query->get('call'),
                'matchers' => $request->query->get('matcher'),
                'users' => $request->query->get('user'),
                'consultants' => $request->query->get('consultant'),
                'node' => $request->query->has('channel') ? $request->query->get('channel') : $request->query->get('node'),
                ];
            if(Payment::methodExists($method)) {
                $filter['methods'] = $method;
                $methods = [$method => Payment::getMethod($method)];
            } else {
                $method = 'global';
                $methods = Payment::getMethods();
                $filter['methods'] = array_keys(array_filter($methods, function($val){
                    return !$val::isInternal();
                }));
            }
            // print_r($filter);die;

            if($target === 'raised') {

                $filter['status'] = Invest::$RAISED_STATUSES;
                $totals[$slot] = $stats->investTotals($filter);

                // Add wallet calc
                $wallet = $stats->investTotals(['types' => 'wallet'] + $filter);
                $totals[$slot]['to_wallet_amount'] = $wallet['amount'];
                $totals[$slot]['to_wallet_invests'] = $wallet['invests'];
                $totals[$slot]['to_wallet_users'] = $wallet['users'];
                // Add projects calc
                $projects = $stats->investTotals(['types' => 'project'] + $filter);
                $totals[$slot]['to_projects_amount'] = $projects['amount'];
                $totals[$slot]['to_projects_invests'] = $projects['invests'];
                $totals[$slot]['to_projects_users'] = $projects['users'];
                // Add matcher wallet calc
                $wallet = $stats->investTotals(['types' => 'matcher_wallet'] + $filter);
                $totals[$slot]['to_matcher_wallet_amount'] = $wallet['amount'];
                $totals[$slot]['to_matcher_wallet_invests'] = $wallet['invests'];
                $totals[$slot]['to_matcher_wallet_users'] = $wallet['users'];
                // Add accumulated in-wallet calc
                $to_wallet = $stats->investTotals(['types' => 'to_wallet', 'status' => Invest::$RAW_STATUSES, 'datetime_from' => null, 'methods' => null] + $filter);
                $from_wallet = $stats->investTotals(['types' => 'from_wallet', 'datetime_from' => null, 'methods' => null] + $filter);
                // print_r($to_wallet);print_r($from_wallet);die;
                $totals[$slot]['in_wallet_amount'] = $to_wallet['amount'] - $from_wallet['amount'];
                $totals[$slot]['in_wallet_invests'] = $to_wallet['invests'] - $from_wallet['invests'];
                $totals[$slot]['in_wallet_users'] = $to_wallet['users'] - $from_wallet['users'];

                $to_matcher_wallet = $stats->investTotals(['types' => 'to_matcher_wallet', 'datetime_from' => null, 'methods' => null] + $filter);
                $from_matcher_wallet = $stats->investTotals(['types' => 'from_matcher_wallet', 'datetime_from' => null, 'methods' => null] + $filter);
                // print_r($to_matcher_wallet);print_r($from_matcher_wallet);die;
                $totals[$slot]['in_matcher_wallet_amount'] = $to_matcher_wallet['amount'] - $from_matcher_wallet['amount'];
                $totals[$slot]['in_matcher_wallet_invests'] = $to_matcher_wallet['invests'] - $from_matcher_wallet['invests'];
                $totals[$slot]['in_matcher_wallet_users'] = $to_matcher_wallet['users'] - $from_matcher_wallet['users'];

                $totals[$slot]['in_users_wallet_amount'] = $totals[$slot]['in_wallet_amount'] - $totals[$slot]['in_matcher_wallet_amount'];
                $totals[$slot]['in_users_wallet_invests'] = $totals[$slot]['in_wallet_invests'] - $totals[$slot]['in_matcher_wallet_invests'];
                $totals[$slot]['in_users_wallet_users'] = $totals[$slot]['in_wallet_users'] - $totals[$slot]['in_matcher_wallet_users'];
                // Add percentages
                if($totals[$slot]['in_wallet_amount']) {
                    $totals[$slot]['in_users_wallet_percent'] = 100 * round($totals[$slot]['in_users_wallet_amount'] / $totals[$slot]['in_wallet_amount'], 4);
                    $totals[$slot]['in_matcher_wallet_percent'] = 100 * round($totals[$slot]['in_matcher_wallet_amount'] / $totals[$slot]['in_wallet_amount'], 4);
                } else {
                    $totals[$slot]['in_users_wallet_percent'] = '--';
                    $totals[$slot]['in_matcher_wallet_percent'] = '--';
                }

            } elseif($target === 'active') {

                $filter['status'] = array_merge(Invest::$ACTIVE_STATUSES, [Invest::STATUS_TO_POOL]);
                $totals[$slot] = $stats->investTotals($filter);

                // Add projects calc
                $projects = $stats->investTotals(['types' => 'project', 'status' => Invest::$ACTIVE_STATUSES] + $filter);
                $wallet = $stats->investTotals(['types' => 'wallet'] + $filter);
                $totals[$slot]['projects_amount'] = $projects['amount'];
                $totals[$slot]['projects_invests'] = $projects['invests'];
                $totals[$slot]['projects_users'] = $projects['users'];
                $totals[$slot]['wallet_amount'] = $wallet['amount'];
                $totals[$slot]['wallet_invests'] = $wallet['invests'];
                $totals[$slot]['wallet_users'] = $wallet['users'];
                // Full commission
                // Platform fees
                $fees = $stats->investFees($filter);
                $totals[$slot]['to_fee_amount'] = $fees['total'];
                // Bank Comissions
                foreach($methods as $i => $m) {
                    $raised = $stats->investTotals(['methods' => $i, 'status' => Invest::$RAISED_STATUSES] + $filter );
                    $returned = $stats->investTotals(['methods' => $i, 'status' => Invest::$RETURNED_STATUSES] + $filter);
                    $totals[$slot]['to_fee_amount'] += $m::calculateComission($raised['invests'], $raised['amount'], $returned['invests'], $returned['amount']);
                }
                // To projects without commissions
                $totals[$slot]['to_projects_amount'] = $projects['amount'] - $totals[$slot]['to_fee_amount'];
                $totals[$slot]['to_projects_invests'] = $projects['invests'];
                $totals[$slot]['to_projects_users'] = $projects['users'];
                // To wallet calc
                $wallet = $stats->investTotals(['status' => Invest::STATUS_TO_POOL] + $filter);
                $totals[$slot]['to_wallet_amount'] = $wallet['amount'];
                $totals[$slot]['to_wallet_invests'] = $wallet['invests'];
                $totals[$slot]['to_wallet_users'] = $wallet['users'];

                // Add percentages
                if($totals[$slot]['amount']) {
                    $totals[$slot]['to_wallet_percent'] = 100 * round($totals[$slot]['to_wallet_amount'] / $totals[$slot]['amount'], 4);
                    $totals[$slot]['to_projects_percent'] = 100 * round($totals[$slot]['to_projects_amount'] / $totals[$slot]['amount'], 4);
                    $totals[$slot]['to_fee_percent'] = 100 * round($totals[$slot]['to_fee_amount'] / $totals[$slot]['amount'], 4);
                } else {
                    $totals[$slot]['to_wallet_percent'] = '--';
                    $totals[$slot]['to_projects_percent'] = '--';
                    $totals[$slot]['to_fee_percent'] = '--';
                }
                // add a nice effective commission percentage
                if($projects['amount']) {
                    $totals[$slot]['fee_percent'] = 100 * round($totals[$slot]['to_fee_amount'] / $projects['amount'], 4);
                } else {
                    $totals[$slot]['fee_percent'] = '--';
                }

            } elseif($target === 'raw') {

                $filter['status'] = Invest::$RAW_STATUSES;
                $totals[$slot] = $stats->investTotals($filter);

            } elseif($target === 'refunded') {
                // Add pool method if global in this case
                if($method === 'global') $filter['methods'][] = 'pool';
                // $filter['status'] = Invest::STATUS_RETURNED;
                $filter['status'] = Invest::$FAILED_STATUSES;
                $totals[$slot] = $stats->investTotals($filter);
                // Add refunded to pool
                $to_pool = $stats->investTotals(['status' => Invest::STATUS_TO_POOL] + $filter);
                $totals[$slot]['to_wallet_amount'] = $to_pool['amount'];
                $totals[$slot]['to_wallet_invests'] = $to_pool['invests'];
                $totals[$slot]['to_wallet_users'] = $to_pool['users'];

                // Add refunded to users
                $to_users = $stats->investTotals(['status' => Invest::$RETURNED_STATUSES] + $filter);
                $totals[$slot]['to_users_amount'] = $to_users['amount'];
                $totals[$slot]['to_users_invests'] = $to_users['invests'];
                $totals[$slot]['to_users_users'] = $to_users['users'];

                // Add percents
                if($totals[$slot]['amount']) {
                    $totals[$slot]['to_users_percent'] = 100 * round($totals[$slot]['to_users_amount'] / $totals[$slot]['amount'], 4);
                    $totals[$slot]['to_wallet_percent'] = 100 * round($totals[$slot]['to_wallet_amount'] / $totals[$slot]['amount'], 4);
                } else {
                    $totals[$slot]['to_users_percent'] = '--';
                    $totals[$slot]['to_wallet'] = '--';
                }
            } elseif($target === 'commissions') {

                // Bank Comissions
                $totals[$slot] = ['charged' => 0, 'lost' => 0 ];
                foreach($methods as $i => $m) {
                    $raised = $stats->investTotals(['methods' => $i, 'status' => Invest::$RAISED_STATUSES] + $filter );
                    $returned = $stats->investTotals(['methods' => $i, 'status' => Invest::$RETURNED_STATUSES] + $filter);
                    $totals[$slot]['charged'] += $m::calculateComission($raised['invests'], $raised['amount'], $returned['invests'], $returned['amount']);
                    $totals[$slot]['lost'] -= $m::calculateComission($returned['invests'], $returned['amount'], $returned['invests'], $returned['amount']);

                }
            } elseif($target === 'fees') {

                // Platform fees
                $filter['status'] = Invest::$ACTIVE_STATUSES;
                $totals[$slot] = $stats->investFees($filter);
                if($totals[$slot]['subtotal']) {
                    $totals[$slot]['user_percent'] = 100 * round($totals[$slot]['user'] / $totals[$slot]['subtotal'], 4);
                    $totals[$slot]['call_percent'] = 100 * round($totals[$slot]['call'] / $totals[$slot]['subtotal'], 4);
                    $totals[$slot]['matcher_percent'] = 100 * round($totals[$slot]['matcher'] / $totals[$slot]['subtotal'], 4);
                } else {
                    $totals[$slot]['user_percent'] = '--';
                    $totals[$slot]['call_percent'] = '--';
                    $totals[$slot]['matcher_percent'] = '--';
                }
                // Global invoice derives bank commissions to the project
                // TODO: move the all to a new api end point "invoice"
                // if($slot === 'all') {
                //     $invoice = $totals['total'];
                //     foreach($methods as $i => $m) {
                //         $raised = $stats->investTotals(['methods' => $i, 'status' => Invest::$RAISED_STATUSES] + $filter);
                //         $returned = $stats->investTotals(['methods' => $i, 'status' => Invest::$FAILED_STATUSES] + $filter);
                //         $invoice +=  $m::calculateComission($raised['invests'], $raised['amount'], $returned['invests'], $returned['amount']);
                //     }
                //     $totals['invoice'] = $invoice;
                // }
            } elseif($target === 'amounts') {

                $filter['status'] = Invest::$ACTIVE_STATUSES;
                $totals[$slot] = $stats->investAmounts($filter);

            } elseif($target === 'matchfunding') {

                foreach(['raised' => Invest::$RAISED_STATUSES, 'active' => Invest::$ACTIVE_STATUSES] as $type => $s) {
                    $filter['status'] = $s;
                    $filter['methods'] = null;
                    $global = $stats->investTotals($filter);

                    // matchfunding global (includes projects)
                    $matchfunding = $stats->investTotals(['types' => 'matchfunding'] + $filter);
                    $totals[$slot][$type.'_matchfunding_amount'] = $matchfunding['amount'];
                    $totals[$slot][$type.'_matchfunding_invests'] = $matchfunding['invests'];
                    $totals[$slot][$type.'_matchfunding_users'] = $matchfunding['users'];

                    $totals[$slot][$type.'_users_amount'] = $global['amount'] - $matchfunding['amount'];
                    $totals[$slot][$type.'_users_invests'] = $global['invests'] - $matchfunding['invests'];
                    $totals[$slot][$type.'_users_users'] = $global['users'] - $matchfunding['users'];


                    // matchfunding alone
                    $match = $stats->investTotals(['types' => 'drop'] + $filter);
                    $totals[$slot][$type.'_from_matchfunding_amount'] = $match['amount'];
                    $totals[$slot][$type.'_from_matchfunding_invests'] = $match['invests'];
                    $totals[$slot][$type.'_from_matchfunding_users'] = $match['users'];

                    $totals[$slot][$type.'_from_users_amount'] = $matchfunding['amount'] - $match['amount'] ;
                    $totals[$slot][$type.'_from_users_invests'] = $matchfunding['invests'] - $match['invests'] ;
                    $totals[$slot][$type.'_from_users_users'] = $matchfunding['users'] - $match['users'] ;

                    // matchfunding from pool (matcher)
                    $matcher = $stats->investTotals(['types' => 'matcher'] + $filter);
                    $totals[$slot][$type.'_from_matcher_amount'] = $matcher['amount'];
                    $totals[$slot][$type.'_from_matcher_invests'] = $matcher['invests'];
                    $totals[$slot][$type.'_from_matcher_users'] = $matcher['users'];

                    // Add some percentages
                    if($global['amount']) {
                        $totals[$slot][$type.'_matchfunding_amount_percent'] = 100 * round($matchfunding['amount'] / $global['amount'], 4);
                        $totals[$slot][$type.'_users_amount_percent'] = 100 - $totals[$slot][$type.'_matchfunding_amount_percent'];
                        $totals[$slot][$type.'_from_matchfunding_amount_percent'] = 100 * round( $match['amount'] / $global['amount'], 4);
                        $totals[$slot][$type.'_from_users_amount_percent'] = 100 - $totals[$slot][$type.'_from_matchfunding_amount_percent'];
                    } else {
                        $totals[$slot][$type.'_matchfunding_amount_percent'] = '--';
                        $totals[$slot][$type.'_users_amount_percent'] = '--';
                        $totals[$slot][$type.'_from_matchfunding_amount_percent'] = '--';
                        $totals[$slot][$type.'_from_users_amount_percent'] = '--';
                    }

                }

            } elseif($target !== 'global') {
                throw new ControllerException("[$target] not found, try one of [raised, active, raw, refunded, commissions, fees, matchfunding]");
            }
        }
        $increments = ['today' => 'yesterday', 'week' => 'last_week', 'month' => 'last_month', 'year' => 'last_year'];
        foreach($totals as $slot => $parts) {
            foreach($parts as $k => $v) {
                // increments
                if(($inc = $increments[$slot]) && is_numeric($v)) {
                    $totals[$slot][$k . '_diff'] = $v - $totals[$inc][$k];
                    if($totals[$inc][$k] && $totals[$inc][$k] != '--') {
                        $totals[$slot][$k . '_gain'] = $totals[$inc][$k] ? round(100 * (($v / $totals[$inc][$k]) - 1), 2) : '--';
                    } else {
                        $totals[$slot][$k . '_gain'] = '--';
                    }
                }
            }
        }
        // Add some formatting
        $emoji = ['🤓','😱','🙄','🤔','☢','💥','🙈'];
        foreach($totals as $slot => $parts) {
            foreach($parts as $k => $v) {
                if(strpos($k, '_gain') !== false || strpos($k, '_percent') !== false) {
                    if($v === '--') {
                        $totals[$slot][$k . '_formatted'] =  $emoji[array_rand($emoji)];
                        continue;
                    }
                    $totals[$slot][$k . '_formatted'] = number_format($v, 2, Currency::get('', 'decimal'), Currency::get('', 'thousands')) . '%';
                }
                elseif(strpos($k, 'amount') !== false || in_array($target, ['fees', 'commissions']))
                    $totals[$slot][$k . '_formatted'] = \amount_format($v);
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
        $ofilter['owner'] = $request->query->get('owner');
        $ofilter['consultant'] = $request->query->get('consultant');
        $ofilter['called'] = $request->query->get('call');
        $ofilter['matcher'] = $request->query->get('matcher');
        $ofilter['node'] = $request->query->has('channel') ? $request->query->get('channel') : $request->query->get('node');
        $filter['status'] = -3; // all projects

        foreach(['created', 'negotiating', 'reviewing', 'published', 'rejected'] as $when) {
            if($part && $part !== $when) continue;
            $date_from = 'created_from';
            $date_until = 'created_until';
            $filter = $ofilter;
            if($when === 'published') {
                $filter['status'] = [Project::STATUS_IN_CAMPAIGN, Project::STATUS_FUNDED, Project::STATUS_FULFILLED, Project::STATUS_UNFUNDED];
                $date_from = 'published_from';
                $date_until = 'published_until';
            }
            elseif($when === 'negotiating') {
                $filter['status'] = -2;
                $date_from = 'updated_from';
                $date_until = 'updated_until';
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
