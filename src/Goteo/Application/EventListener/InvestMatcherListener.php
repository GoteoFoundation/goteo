<?php

namespace Goteo\Application\EventListener;

use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\User;

//

class InvestMatcherListener extends AbstractListener {

	public function onInvestSuccess(FilterInvestRequestEvent $event) {
		$method   = $event->getMethod();
		$response = $event->getResponse();
		$invest   = $method->getInvest();
		$project  = $invest->getProject();

        // Only for invests on projects
        if(!$project) {
            return;
        }

        // Check if project is in a matcher campaign
        if($matcher = Matcher::getFromProject($project)) {
            print_r($matcher);
            die($matcher->getTotalAmount());
            // TODO: find processor, and execute it
        }
	}

	public static function getSubscribedEvents() {
		return array(
            AppEvents::INVEST_SUCCEEDED => ['onInvestSuccess', -2], // low priority (after general processing)
            AppEvents::INVEST_INIT_REQUEST => ['onInvestSuccess', -2], // for testing only
		);
	}
}
