<?php

namespace Goteo\Util\Stats;
use Goteo\Model\User;
use Goteo\Model\Project;
use Goteo\Model\Invest;
use Goteo\Model\Call;
use Goteo\Model\Matcher;

class Stats {
	static public function create() {
		return new self();
	}

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
		$calls_raised=Call::getTotalRaised();
		$matcher_raised=Matcher::getTotalRaised();

		return $calls_raised+$matcher_raised;
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
	

}