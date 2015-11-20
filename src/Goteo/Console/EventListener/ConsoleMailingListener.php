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
use Goteo\Console\Event\FilterMailingEvent;
use Goteo\Console\Event\FilterSendmailEvent;

use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

//

class ConsoleMailingListener extends AbstractListener {

	public function onSendMail(FilterSendmailEvent $event) {
		$recipient = $event->getRecipient();

		// $this->info('', []);
		$errors = [];
		if ($recipient->send($errors, ['blocked'])) {
			$this->notice("Mail recipient sent successfully", [$recipient, 'recipient_user' => $recipient->user]);
		} else {
			throw new \LogicException("Error sending mail [".implode(', ', $errors));
		}

	}

	public function onMailingStart(FilterMailingEvent $event) {
		$mailing = $event->getSender();

		$this->info("Sending massive mailing", [$mailing]);
	}

	public function onMailingFinish(FilterMailingEvent $event) {
		$mailing = $event->getSender();

		// feed event
		$log = new Feed();
		// We don't want to repeat this feed
		$log->unique = true;
		$log->setTarget($mailing->id, 'mailing')
			->populate('feed-mailing-massive', '/admin/mailing/newsletter',
			new FeedBody(null, null, 'feed-mailing-massive-completed', ['%ID%' => $mailing->id, '%SUBJECT%' => $mailing->subject])
		)
		->doAdmin('system');

		if ($log->unique_issue) {
            $this->warning("Duplicated feed", [$mailing, $log]);
        } else {
			$this->notice("Populated feed", [$mailing, $log]);
        }

		// $this->info('', []);
		$errors = [];
		if (!$mailing->setActive(false)->active) {
			$this->notice("Massive mailing sent successfully", [$mailing, 'errors' => $errors]);
		} else {
			$this->error("Error deactivating mailing", [$mailing]);
		}

	}

	public function onMailingAbort(FilterMailingEvent $event) {
		$mailing = $event->getSender();

		if ($mailing->blocked) {
			$mailing->setLock(false);
			$this->info("Unlocking massive mailing", [$mailing, 'quota' => $limit]);
			if ($mailing->setLock(false)->blocked) {
				throw new \RuntimeException("Error unlocking mailing [{$mailing->id}]");
			}
		}

		throw new \RuntimeException($event->getMessage());

		// $this->info('', []);
		$errors = [];
		// if($mailing->send($errors) ) {
		//     $this->info("Mail mailing sent successfully to [{$mailing->email}]", [$mailing, 'errors' =>  $errors]);
		// } else {
		//     $this->error("Error sending mail", [$mailing, 'errors' =>  $errors]);
		// }

	}

	public static function getSubscribedEvents() {
		return array(
			ConsoleEvents::MAILING_SENDMAIL => 'onSendMail',
			ConsoleEvents::MAILING_STARTED  => 'onMailingStart',
			ConsoleEvents::MAILING_FINISHED => 'onMailingFinish',
			ConsoleEvents::MAILING_ABORTED  => 'onMailingAbort',
		);
	}
}
