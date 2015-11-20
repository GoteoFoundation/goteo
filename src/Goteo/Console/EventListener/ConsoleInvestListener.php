<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\EventListener;

use Goteo\Application\EventListener\AbstractListener;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterInvestRefundEvent;
use Goteo\Model\Invest;
use Goteo\Model\User\Pool;

//

class ConsoleInvestListener extends AbstractListener {

	/**
	 * Cancels and invest for other reasons than failed projects
	 * @param  FilterInvestRefundEvent $event
	 */
	public function onInvestRefundCancel(FilterInvestRefundEvent $event) {
		$method = $event->getMethod();
		$invest = $event->getInvest();

		$this->info('Invest refund cancel', [$invest, 'pool' => $invest->pool, 'project' => $invest->project, 'user' => $invest->user]);

		if ($invest->cancel(false)) {
			Invest::setDetail($invest->id, $method::getId().'-cancel', 'Invest manually cancelled successfully');
			// update cached data
			$invest->keepUpdated();
		} else {
			Invest::setDetail($invest->id, $method::getId().'-cancel-fail', 'Error while cancelling invest');
		}

	}

	/**
	 * Cancels and invest for failed projects
	 * @param  FilterInvestRefundEvent $event
	 */
	public function onInvestRefundReturn(FilterInvestRefundEvent $event) {
		$method = $event->getMethod();
		$invest = $event->getInvest();

		$this->info('Invest refund return', [$invest, 'pool' => $invest->pool, 'project' => $invest->project, 'user' => $invest->user]);
		if ($invest->cancel(true)) {
			Invest::setDetail($invest->id, $method::getId().'-cancel', 'Invest automatically refunded successfully');
			// update cached data
			$invest->keepUpdated();
		} else {
			Invest::setDetail($invest->id, $method::getId().'-cancel-fail', 'Error while cancelling invest');
		}

	}
	/**
	 * Handles failed refund process
	 * @param  FilterInvestRefundEvent $event
	 */
	public function onInvestRefundFailed(FilterInvestRefundEvent $event) {
		$method = $event->getMethod();
		$invest = $event->getInvest();

		$response = $event->getResponse();
		$this->info('Invest refund failed', [$invest, 'pool' => $invest->pool, 'project' => $invest->project, 'user' => $invest->user, 'message' => $response->getMessage()]);
		Invest::setDetail($invest->id, $method::getId().'-return-fail', 'Error while refunding invest: '.$response->getMessage());

	}

	public static function getSubscribedEvents() {
		return array(
			ConsoleEvents::INVEST_CANCELLED     => 'onInvestRefundCancel',
			ConsoleEvents::INVEST_CANCEL_FAILED => 'onInvestRefundFailed', // same action as return at this moment
			ConsoleEvents::INVEST_RETURNED      => 'onInvestRefundReturn',
			ConsoleEvents::INVEST_RETURN_FAILED => 'onInvestRefundFailed',
		);
	}
}
