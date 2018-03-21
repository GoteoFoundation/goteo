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
		return Project::getSucessfulPercentage(true);
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
    public function getInvestsTotals($filter = [], $count = 'all') {
        return Invest::getList($filter, null, 0, 0, $count);
    }

    /**
     * Handy method to obtain cached totals from projects
     */
    public function getProjectsTotals($filter = [], $count = 'all') {
        return Project::getList($filter, null, 0, 0, $count);
    }

}
