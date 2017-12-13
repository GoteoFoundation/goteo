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
use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Console\UsersSend;
use Goteo\Library\Currency;
use Goteo\Library\Feed;
use Goteo\Library\FeedBody;

use Goteo\Model\Invest;
use Goteo\Model\Project;

//

class ConsoleProjectListener extends AbstractListener {

	private function logFeedEntry(Feed $log, Project $project) {
		if ($log->unique_issue) {
			$this->warning("Duplicated feed", [$project, $log]);
		} else {
			$this->notice("Populated feed", [$project, $log]);
		}
	}

	/**
	 * Adds some actions for a failed project
	 * Should be triggered when project reaches his end-life and fails
	 * @param  FilterProjectEnding $event
	 */
	public function onProjectFailed(FilterProjectEvent $event) {
		$project = $event->getProject();

		$errors = [];
		if (!$project->fail($errors)) {
			$this->critical("Error archiving project", [$project, 'errors' => $errors]);
			return;
		}
		$this->info("Project archived", [$project, 'closed' => $project->closed]);

		$symbol = Currency::getDefault('html');

		$log = new Feed();
		// We don't want to repeat this feed
		$log->unique = true;
		$log->setTarget($project->id)
			->populate('feed-project-archived', '/admin/projects',
			new FeedBody(null, null, 'feed-project-has-failed', [
					'%PROJECT%'   => Feed::item('project', $project->name, $project->id),
					'%FAILED%'    => new FeedBody('relevant', null, 'feed-project-failed'),
					'%MONEY%'     => new FeedBody('money', null, 'feed-project-money', [
							'%AMOUNT%'  => $project->amount." $symbol",
							'%PERCENT%' => $project->getAmountPercent()
						])
				]),
			$project->image)
			->doAdmin('project');

		$this->logFeedEntry($log, $project);
		// public event
		$log->unique_issue = false;
		$log->title        = $project->name;
		$log->url          = '/project/'.$project->id;
		$log->setBody(new FeedBody(null, null, 'feed-project_fail', [
					Feed::item('project', $project->name, $project->id),
					$project->amount." $symbol",
					$project->getAmountPercent()
				])
		);

		$log->doPublic('projects');

		$this->logFeedEntry($log, $project);
		// Email de proyecto fallido al autor, inversores y destinatarios de recompensa
		UsersSend::toOwner('fail', $project);
		UsersSend::toInvestors('fail', $project, [Invest::STATUS_CHARGED]);
		// Gift reward, currently not active
		// UsersSend::toFriends('fail', $project);

	}

	/**
	 * Adds some actions for a project when reaches successfully the first round (ie: end of life) for one-round-only projects
	 * @param  FilterProjectEnding $event
	 */
	public function onProjectOneRound(FilterProjectEvent $event) {
		$project = $event->getProject();

        if(!empty($project->success) && $project->success != '0000-00-00') {
            $this->error("Error marking project as funded ", [$project, 'errors' => 'Already funded']);
            return;
        }

        $this->info("Ending life for one-round-only project", [$project]);

        // Set pass date
		$errors = array();
		if ($project->passDate($errors)) {
            $this->notice("Project its only round successfully", [$project]);

        } else {
            $this->critical("Error passing project unique round", [$project, 'errors' => $errors]);
        }

        // mark as succeeded
        if ($project->succeed($errors)) {
			$this->notice("Project funded successfully", [$project]);
        } else {
			$this->critical("Error marking project as funded", [$project, 'errors' => $errors]);
			return;
		}

		$this->info("Project marked as funded successfully", [$project]);

		$log = new Feed();
		// We don't want to repeat this feed
		$log->unique = true;
		$log->setTarget($project->id);
		$log->populate('feed-project-one-round-passed', '/admin/projects',
			new FeedBody(null, null, 'feed-project-one-round-succeeded', [
					'%PROJECT%'   => Feed::item('project', $project->name, $project->id),
					'%SUCCEEDED%' => new FeedBody('relevant', null, 'feed-project-succeeded'),
					'%MONEY%'     => new FeedBody('money', null, 'feed-project-money', [
							'%AMOUNT%'  => $project->amount." $symbol",
							'%PERCENT%' => $project->getAmountPercent()
						])
				]),
			$project->image
		)
			->doAdmin('project');
		$this->logFeedEntry($log, $project);
		// public event
		$log->unique_issue = false;
		$log->title        = $project->name;
		$log->url          = '/project/'.$project->id;
		$log->setBody(new FeedBody(null, null, 'feed-project_finish_unique', [
					Feed::item('project', $project->name, $project->id),
					$project->amount." $symbol",
					$project->getAmountPercent()
				])
		);

		$log->doPublic('projects');

		$this->logFeedEntry($log, $project);
		// Email de proyecto finaliza su única ronda al autor y a los inversores
		UsersSend::toOwner('unique_pass', $project);
		UsersSend::toInvestors('unique_pass', $project);

	}

	/**
	 * Adds some actions for a project when reaches successfully the first round for 2 rounds projects
	 *
	 * @param  FilterProjectEnding $event
	 */
	public function onProjectRound1(FilterProjectEvent $event) {
		$project = $event->getProject();

        if(!empty($project->passed) && $project->passed != '0000-00-00') {
            $this->error("Error passing project to second round", [$project, 'errors' => 'Already passed']);
            return;
        }

        $this->info("Passing round for 2-rounds project", [$project]);
        // Set pass date
        $errors = array();
        if ($project->passDate($errors)) {
            $this->notice("Project passed to second round successfully", [$project]);

        } else {
			$this->critical("Error passing project to second round", [$project, 'errors' => $errors]);
			return;
		}

		$log = new Feed();
		// We don't want to repeat this feed
		$log->unique = true;
		$log->setTarget($project->id);
		$log->populate('feed-project-round1-passed', '/admin/projects',
			new FeedBody(null, null, 'feed-project-round1-succeeded', [
					'%PROJECT%'   => Feed::item('project', $project->name, $project->id),
					'%ROUND1%'    => new FeedBody('relevant', null, 'feed-project-round1'),
					'%MONEY%'     => new FeedBody('money', null, 'feed-project-money', [
							'%AMOUNT%'  => $project->amount." $symbol",
							'%PERCENT%' => $project->getAmountPercent()
						])
				]),
			$project->image
		)
			->doAdmin('project');
		$this->logFeedEntry($log, $project);
		// public event
		$log->unique_issue = false;
		$log->title        = $project->name;
		$log->url          = '/project/'.$project->id;
		$log->setBody(new FeedBody(null, null, 'feed-project_goon', [
					'%PROJECT%' => Feed::item('project', $project->name, $project->id),
					'%AMOUNT%'  => $project->amount." $symbol",
					'%PERCENT%' => $project->getAmountPercent()
				])
		);

		$log->doPublic('projects');

		$this->logFeedEntry($log, $project);
		// Email de proyecto pasa a segunda ronda al autor y a los inversores
		UsersSend::toOwner('r1_pass', $project);
		UsersSend::toInvestors('r1_pass', $project);

	}

	/**
	 * Adds some actions for a project when reaches successfully the second round for 2 rounds projects
	 *
	 * @param  FilterProjectEnding $event
	 */
	public function onProjectRound2(FilterProjectEvent $event) {
		$project = $event->getProject();

        if(!empty($project->success) && $project->success != '0000-00-00') {
            $this->error("Error marking project as funded ", [$project, 'errors' => 'Already funded']);
            return;
        }

		$this->info("Ending life for 2 rounds project", [$project]);
		// mark as succeeded
		$errors = [];
		if ($project->succeed($errors)) {
            $this->notice("Project funded successfully", [$project]);
        } else {
			$this->critical("Error marking project as funded", [$project, 'errors' => $errors]);
			return;
		}

		$this->info("Project marked as funded successfully", [$project]);

		$log = new Feed();
		// We don't want to repeat this feed
		$log->unique = true;
		$log->setTarget($project->id);
		$log->populate('feed-project-round2-passed', '/admin/projects',
			new FeedBody(null, null, 'feed-project-round2-succeeded', [
					'%PROJECT%'   => Feed::item('project', $project->name, $project->id),
					'%SUCCEEDED%' => new FeedBody('relevant', null, 'feed-project-succeeded'),
					'%MONEY%'     => new FeedBody('money', null, 'feed-project-money', [
							'%AMOUNT%'  => $project->amount." $symbol",
							'%PERCENT%' => $project->getAmountPercent()
						])
				]),
			$project->image
		)
			->doAdmin('project');
		$this->logFeedEntry($log, $project);
		// public event
		$log->unique_issue = false;
		$log->title        = $project->name;
		$log->url          = '/project/'.$project->id;
		$log->setBody(new FeedBody(null, null, 'feed-project_finish', [
					Feed::item('project', $project->name, $project->id),
					$project->amount." $symbol",
					$project->getAmountPercent()
				])
		);

		$log->doPublic('projects');

		$this->logFeedEntry($log, $project);
		//Email de proyecto final segunda ronda al autor y a los inversores
		UsersSend::toOwner('r2_pass', $project);
		UsersSend::toInvestors('r2_pass', $project);

	}

	public static function getSubscribedEvents() {
		return array(
			ConsoleEvents::PROJECT_FAILED    => 'onProjectFailed',
			ConsoleEvents::PROJECT_ONE_ROUND => 'onProjectOneRound',
			ConsoleEvents::PROJECT_ROUND1    => 'onProjectRound1',
			ConsoleEvents::PROJECT_ROUND2    => 'onProjectRound2',
		);
	}
}
