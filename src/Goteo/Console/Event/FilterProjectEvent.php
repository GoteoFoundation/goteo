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

use Goteo\Model\Project;

use Symfony\Component\EventDispatcher\Event;

class FilterProjectEvent extends Event {
	protected $project;

	public function __construct(Project $project) {
		$this->project = $project;
	}

	public function getProject() {
		return $this->project;
	}
}
