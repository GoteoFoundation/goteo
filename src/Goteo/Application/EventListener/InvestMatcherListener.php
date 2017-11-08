<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */


namespace Goteo\Application\EventListener;

use Goteo\Application\App;
use Goteo\Application\AppEvents;
use Goteo\Application\Event\FilterInvestRequestEvent;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;
use Goteo\Library\Text;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\User;
use Goteo\Util\MatcherProcessor\MatcherProcessorException;

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
        if($matchers = Matcher::getFromProject($project)) {
            foreach($matchers as $matcher) {
                // find processor, and execute it
                if($processor = App::getService('app.matcher.finder')->getProcessor($matcher)) {
                    $processor->setInvest($invest);
                    $processor->setProject($project);
                    $processor->setMethod($method);
                    try {
                        $invests = $processor->getInvests();
                        print_r($invests);die;
                        $this->notice("Matcher has invests", [$matcher, $invest, 'matcher_processor' => $matcher->processor]);
                    } catch(MatcherProcessorException $e) {
                        $this->notice("No invests for Matcher", [$matcher, $invest, 'matcher_processor' => $matcher->processor, 'reason' => $e->getMessage()]);
                    }
                }
            }
        }
	}

	public static function getSubscribedEvents() {
		return array(
            AppEvents::INVEST_SUCCEEDED => ['onInvestSuccess', -2], // low priority (after general processing)
            AppEvents::INVEST_INIT_REQUEST => ['onInvestSuccess', -2], // for testing only
		);
	}
}
