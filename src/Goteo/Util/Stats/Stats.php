<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Util\Stats;

use Goteo\Application\Config;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\Call;
use Goteo\Model\Matcher;
use Goteo\Library\Cacher;

class Stats {
    private
        $cacher,
        $ttl = 1800, // 1/2 hour cache by default
        $debug = false;

    public function __construct(Cacher $cacher, $ttl = 1800) {
        $this->cacher = $cacher;
        $this->ttl = $ttl;
    }

	static public function create($name = 'generic', $ttl = 1800) {
		return new self(new Cacher($name), $ttl);
	}

    /**
     * Allows to automatically use cache by calling methods in this class
     * without the "get" prefix. ie:
     *     - Instead of call ->getTotalUsers() , just call ->totalUsers()
     */
    public function __call($name, $arguments) {
        $method = 'get' . ucfirst($name);
        if(method_exists($this, $method)) {
            if ($this->cacher && !$this->debug) {
                $key = $this->cacher->getKey($arguments, $method);
                $result = $this->cacher->retrieve($key);
            }
            if(empty($result)) {
                $result =  call_user_func_array([$this, $method], $arguments);
                // sets cache
                if($this->cacher && !$this->debug) {
                    $this->cacher->store($key, $result, $this->ttl);
                }
            }

            return $result;
        }
        throw new \RuntimeException("Method [$method] does not exists in this class!");
    }

    /**
     * You should call next functions without the "get" prefix to take
     * advantage of caching
     */

	public function getTotalUsers() {
		return User::getTotalUsers();
	}

	public function getSucessfulPercentage() {
		return Project::getSucessfulPercentage();
	}

	public function getTotalMoneyFunded() {
		return Invest::getTotalMoneyFunded();
	}

	public function getTotalInvestAverage() {
		return Project::getTotalInvestAverage();
	}

	public function getMatchfundingRaised() {
		$calls_raised = Call::getTotalRaised();
		$matcher_raised = Matcher::getTotalRaised();

		return $calls_raised + $matcher_raised;
	}

	public function getMatchfundingSucessfulPercentage() {
		return Project::getSucessfulPercentage('all');
	}

	public function getAdvisedProjects() {
		return Project::getAdvisedProjects();
	}

	public function getFundedProjects() {
		return Project::getFundedProjects();
	}

	public function getMatchfundingOwnersGender() {
		return Project::getMatchfundingOwnersGender();;
	}

    /**
     * Handy method to obtain cached totals from invests
     */
    public function getInvestTotals($filter = [], $count = 'all') {
        $totals = Invest::getList($filter, null, 0, 0, $count);
        return $totals;
    }

    public function getInvestFees($filter = []) {
        $totals = Invest::calculateFees($filter);
        // print_r($filter);print_r($totals);
        // Add some extra useful calcs
        foreach($totals as $k => $a) {
            $totals['subtotal'] += $a;
            $totals['vat'] += ((float)Config::get('vat')) * $a / 100;
        }
        $totals['total'] = $totals['subtotal'] + $totals['vat'];
        return $totals;
    }

    public function getInvestAmounts($filter, $div = 10) {

        list($sqlFilter, $values) = Invest::getSQLFilter($filter);
        $sql = "SELECT CONCAT(10 * (amount DIV $div),'-',($div * (amount DIV $div) + $div)) AS amount,
            COUNT(id) AS total, ROUND(100*COUNT(id)/(SELECT COUNT(id) FROM invest $sqlFilter ), 2) AS percent
            FROM invest $sqlFilter
            GROUP BY $div * (amount DIV $div)
            HAVING percent > 0.1
            ORDER BY percent DESC";
        // die(\sqldbg($sql, $values));die;
        $totals = [];
        if($res = Invest::query($sql, $values)->fetchAll(\PDO::FETCH_OBJ)) {
            foreach($res as $ob) {
                $totals[] = ['amount' => $ob->amount, 'total' => (int) $ob->total, 'percent' => round($ob->percent,2)];
            }
        }
        return $totals;
    }
    /**
     * Handy method to obtain cached totals from projects
     */
    public function getProjectTotals($filter = [], $count = 'all') {
        return Project::getList($filter, null, 0, 0, $count);
    }

}
