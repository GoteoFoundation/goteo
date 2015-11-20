<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\Event;

use Goteo\Model\Mail\SenderRecipient as Recipient;

use Symfony\Component\EventDispatcher\Event;

class FilterSendmailEvent extends Event {
	protected $recipient;

	public function __construct(Recipient $recipient) {
		$this->recipient = $recipient;
	}

	public function getRecipient() {
		return $this->recipient;
	}
}
