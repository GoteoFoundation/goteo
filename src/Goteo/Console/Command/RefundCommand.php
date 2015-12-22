<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Console\Command;

use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterInvestRefundEvent;
use Goteo\Model\Invest;

use Goteo\Model\Project;

use Goteo\Payment\Payment;

use Goteo\Util\Omnipay\Message\EmptySuccessfulResponse;
use Omnipay\Common\Message\ResponseInterface;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefundCommand extends AbstractCommand {

	protected function configure() {
		$this->setName("refund")
		     ->setDescription("Processes refunds for failed projects")
		     ->setDefinition(array(
				new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
				new InputOption('force', 'f', InputOption::VALUE_NONE, 'Force the invest update to a refund or cancelled status, even if it fails for any reason'),
				new InputOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Only processes the specified Project ID'),
				new InputOption('invest', 'i', InputOption::VALUE_OPTIONAL, 'Only processes the specified Invest ID'),
				new InputOption('any-project', 'a', InputOption::VALUE_NONE, 'Does not check for project to be in an UNFUNDED status'),
			))
		->setHelp(<<<EOT
This script proccesses refunds for payments no yet refunded for archived projects
A failed project will refund payments to the backers.

Usage:

Processes pending refunds for archived (failed) projects in read-only mode
<info>./console refund</info>

Processes pending refunds for archived (failed) projects and write operations to database
<info>./console refund --update</info>

Processes pending refunds for archived (failed) projects and write operations to database
Also changes the Invest status to cancelled or refunded even if the refund operation fails
<info>./console refund --update --force</info>

Processes pending refunds in read-only mode for project demo-project
<info>./console refund --project demo-project</info>

Processes refunds for Invest 12345
<info>./console refund --invest 12345</info>


EOT
		)
		;

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$update      = $input->getOption('update');
		$project_id  = $input->getOption('project');
		$invest_id   = $input->getOption('invest');
		$force       = $input->getOption('force');
		$any_project = $input->getOption('any-project');

        if ($invest_id) {
            $output->writeln("<info>Processing Invest [$invest_id]:</info>");
            $invests = [Invest::get($invest_id)];
        } else {
            if ($project_id) {
                $output->writeln("<info>Processing project [$project_id] with pending refunds:</info>");
            } else {
                $output->writeln('<info>Processing projects archived with pending refunds:</info>');
            }
            $invests = Invest::getList(['methods' => null,
                    'status'                            => Invest::STATUS_CHARGED,
                    'projectStatus'                     => $any_project ? null : Project::STATUS_UNFUNDED,
                    'projects'                          => $project_id
                ], null, 0, 10000);

            // $output->writeln("update [$update] project[$project_id] invest[$invest_id] force[$force] any-project[$any_project]");

        }

        if (!$invests) {
            $this->error("No invests found!");
            return 1;
        }

		$processed = 0;
		foreach ($invests as $invest) {
			$project             = $invest->getProject();
			$returned            = ($project->status == Project::STATUS_UNFUNDED);
			$event_refund        = $returned ? ConsoleEvents::INVEST_RETURNED : ConsoleEvents::INVEST_CANCELLED;
			$event_refund_failed = $returned ? ConsoleEvents::INVEST_RETURN_FAILED : ConsoleEvents::INVEST_CANCEL_FAILED;

			if ((int) $invest->status !== Invest::STATUS_CHARGED) {
				$this->debug("Skipping status [{$invest->status}]. Only CHARGED status will be processed", [$invest, 'project' => $invest->project, 'user' => $invest->user]);
				continue;
			}
			if (!Payment::methodExists($invest->method)) {
				$this->debug("Skipping non existing method {$invest->method}", [$invest, 'project' => $invest->project, 'user' => $invest->user]);
				continue;
			}

			$this->info("Processing active invest", [$invest, 'project' => $invest->project, 'user' => $invest->user, 'invested' => $invest->invested, 'preapproval' => $invest->preapproval]);

			// get my money back!!
			if ($update) {
				$method = $invest->getMethod();

				if ($invest->pool) {
					$this->notice("Invest refund goes to pool", [$invest, 'project' => $invest->project, 'user' => $invest->user]);
					$response = new EmptySuccessfulResponse('Invest to Pool refund');
				} elseif ($method->refundable()) {
					// process gateway refund
					// go to the gateway, gets the response
					$response = $method->refund();
				} elseif ($force) {
					$this->warning("Forcing method: {$invest->method} as is not refundable. FORCING SUCCESSFUL REFUND", [$invest, 'project' => $invest->project, 'user' => $invest->user]);
					$response = new EmptySuccessfulResponse('Forced refund');
				} else {
					$this->error("Skipping method: {$invest->method} as is not refundable.", [$invest, 'project' => $invest->project, 'user' => $invest->user]);
					continue;
				}
				// Checks and redirects
				if (!$response instanceof ResponseInterface) {
					throw new \RuntimeException('This response does not implements ResponseInterface.');
				}

				// On-sites can return a successful response here
				if ($response->isSuccessful()) {
					if ($force) {
						$this->warning("Forcing method: {$invest->method}. Original refund FAILED. FORCING SUCCESSFUL REFUND", [$invest, 'project' => $invest->project, 'user' => $invest->user]);
					}
					// Event invest success
					$invest = $this->dispatch($event_refund, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
				} else {
					$invest = $this->dispatch($event_refund_failed, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
					$this->error('Error refunding invest', [$invest, 'project' => $invest->project, 'user' => $invest->user, 'message' => $response->getMessage()]);
					continue;
				}

				// New Invest Refund Event
				if (in_array($invest->status, [Invest::STATUS_RETURNED, Invest::STATUS_CANCELLED, Invest::STATUS_TO_POOL])) {
					$this->notice('Invest refunded successfully', [$invest, 'project' => $invest->project, 'user' => $invest->user, 'message' => $response->getMessage()]);
				} else {
					$this->error('Error refunding invest', [$invest, 'project' => $invest->project, 'user' => $invest->user, 'message' => $response->getMessage()]);
				}

			}
			$processed++;
		}

		if ($processed == 0) {
			$this->info("No invests processed");
			return;
		}

		if (!$update) {
			$this->warning('Dummy execution. No write operations done');
			$output->writeln('<comment>No write operations done. Please execute the command with the --update modifier to perform write operations</comment>');
		}

	}
}
