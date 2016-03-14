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

    /**
     * Get days since published date
     */
    public function getDays() {
        $date1 = new \DateTime($this->project->published);
        $date2 = new \DateTime();

        return $date2->diff($date1)->format("%a");
    }

    /**
     * Get days since succeeded date in one round projects or passed date in 2 rounds
     */
    public function getDaysSucceeded() {
        $date = $this->project->success;
        $date1 = new \DateTime($date);
        $date2 = new \DateTime();

        return $date2->diff($date1)->format("%a");
    }
    /**
     * Get days since project is funded
     * @return [type] [description]
     */
    public function getDaysFunded() {
        $date = $this->project->one_round ? $this->project->success : $this->project->passed;
        $date1 = new \DateTime($date);
        $date2 = new \DateTime();

        return $date2->diff($date1)->format("%a");
    }
}
