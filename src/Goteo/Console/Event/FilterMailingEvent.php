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

use Goteo\Model\Mail\Sender;

use Symfony\Component\EventDispatcher\Event;

class FilterMailingEvent extends Event {
	protected $mailing;
	protected $message;

	public function __construct(Sender $mailing, $message = 'Unknow reason') {
		$this->mailing = $mailing;
		$this->message = $message;
	}

	public function getSender() {
		return $this->mailing;
	}

	public function getMessage() {
		return $this->message;
	}
}
