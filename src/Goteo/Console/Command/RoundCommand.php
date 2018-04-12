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
use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Application\Currency;
use Goteo\Application\Config;
use Goteo\Model\Invest;
use Goteo\Model\Project;

use Symfony\Component\Console\Input\ArrayInput;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class RoundCommand extends AbstractCommand {

	protected function configure() {
		$this->setName("endround")
		     ->setDescription("Project status changer for 1st and 2on round")
		     ->setDefinition(array(
				new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
				new InputOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Only processes the specified Project ID'),
                new InputOption('skip-invests', 's', InputOption::VALUE_NONE, 'Do not processes Invests returns'),
				new InputOption('predict', 't', InputOption::VALUE_REQUIRED, 'Try to predict the endround in the number of days specified'),
			))
		->setHelp(<<<EOT
This script proccesses active projects reaching ending rounds.
A failed project will change his status to failed.
A successful project which reached his first round will start a second round.
A successful project which reached his second round will end his invest life time.

Usage:

Processes pending projects in read-only mode
<info>./console endround</info>

Processes pending projects and write operations to database
<info>./console endround --update</info>

Processes projects demo-project only and write operations to database
<info>./console endround --project demo-project --update</info>

Processes projects demo-project only and write operations to database but
does not execute refunds on the related invests
<info>./console endround --project demo-project --skip-invests --update</info>

Says if a project will change status 1 day before real end
<info>./console endround --predict 1 --update</info>

EOT
		)
		;

	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$update       = $input->getOption('update');
		$project_id   = $input->getOption('project');
        $skip_invests = $input->getOption('skip-invests');
		$predict = $input->getOption('predict');

		if ($project_id) {
			$output->writeln("<info>Processing Project [$project_id]:</info>");
			$projects = [Project::get($project_id)];
		} else {
			$output->writeln('<info>Processing Active Projects:</info>');

			// Active projects
			$projects = Project::getList(['status' => Project::STATUS_IN_CAMPAIGN], null, 0, 10000);
		}

        if($predict) {
            if($update) {
                throw new \RuntimeException("Option --predict cannot be executed with --update");
            }
            foreach($projects as $p) {
                $published = new \Datetime($p->published);
                $published->modify("-$predict day");
                $new = $published->format("Y-m-d");
                if($new != $p->published) {
                    $p->old_published = $p->published;
                    $p->old_days_active = $p->days_active;
                    $p->published = $new;
                    $p->setDays();
                }
            }
        }

        // print_r($projects->days_active);die(date("Y-m-d H:i\n"));

		if (!$projects) {
			$this->info("No projects found");
			return;
		}

		$symbol    = Currency::getDefault('id');
		$processed = 0;
        $action_done = false;
        $returnd_code = 0;
        foreach ($projects as $project) {
            if ((int) $project->status !== Project::STATUS_IN_CAMPAIGN) {
                $this->debug("Skipping status [{$project->status}] from PROJECT: {$project->id}. Only projects IN CAMPAIGN will be processed");
                continue;
            }

            // Make sure amounts are correct
            $project->amount = Invest::invested($project->id);
            $percent         = $project->getAmountPercent();

			$this->debug("Processing project in campaign", [$project, "project_days_active" => $project->days_active, 'project_days_round1'  => $project->days_round1,
                    'project_days_round2'  => $project->days_round2, "percent" => $percent]);


            // Check project's health
            if ($project->days_active >= $project->days_round1) {
                // si no ha alcanzado el mínimo, pasa a estado caducado
                if ($project->amount < $project->mincost) {
                    $action_done = true;
                    if($predict) {
                        $output->writeln("Mocking project <info>{$project->id}</info> from published at <comment>{$project->old_published}</comment> to <fg=red>{$project->published}</>. From days active <comment>{$project->old_days_active}</comment> to <fg=red>{$project->days_active}</>\n");

                        $text = $predict == 1 ? "tonight" : "in $predict days.";
                        $output->writeln("The project [<info>{$project->name}</info>] will be <fg=red>ARCHIVED</> $text");
                        $output->writeln("Amount: <fg=blue>" . \amount_format($project->amount) . '</>');
                        $output->writeln("Minimum: <fg=blue>" . \amount_format($project->mincost) . '</>');
                        $output->writeln("Percent achieved: <fg=red>" . round(100 * $project->amount / $project->mincost, 2).'%</>');
                        $output->writeln("Project page: " . Config::getUrl() . "/project/{$project->id}");
                        $output->writeln("\n---\n");
                    } else {
                        $this->notice("Archiving project. It has FAILED on achieving the minimum amount", [$project]);
                    }
                    if ($update) {
                        // dispatch ending event, will generate a feed entry if needed
                        $project = $this->dispatch(ConsoleEvents::PROJECT_FAILED, new FilterProjectEvent($project))->getProject();
                    }

                    if ($skip_invests) {
                        $this->warning("Skipping Invests refunds as required\n--");
                    } else {
                        // Execute "console refund"  command for returning invests
                        // refund Invests for this project
                        $args = ' --project ' . escapeshellarg($project->id);
                        if($update) $args .= ' --update';
                        else        $args .= ' --any-project';
                        if($output->isVerbose()) $args .= ' --verbose';
                        $process = new Process(GOTEO_PATH . 'bin/console refund' . $args);
                        $process->setTimeout(null); // no time limit
                        $process->run(function ($type, $buffer) use ($output) {
                            if (Process::ERR === $type) {
                                $output->write("<error>$buffer</error>");
                            } else {
                                $output->write("<fg=magenta>$buffer</>");
                            }
                        });
                        if($process->isSuccessful()) {
                            $this->notice('Subcommand processed successfully', ['command' => $process->getCommandLine()]);
                        } else {
                            $this->error('Subcommand failed', ['command' => $process->getCommandLine()]);
                        }

                        //
                        // NOT USING SUB-COMMAND CALLING BECAUSE EVENTS ARE NOT FIRED
                        //
                        // $command = $this->getApplication()->find('refund');

                        // // Changes the name of the logger:
                        // // $command->addLogger(new \Monolog\Logger('refund', $this->getLogger()->getHandlers(), $this->getLogger()->getProcessors()), 'refund');
                        // // lOG UNDER THE SAME NAME
                        // $command->addLogger($this->getLogger(), 'refund');

                        // $arguments = array(
                        //     'command'   => 'refund',
                        //     '--project' => $project->id,
                        // );
                        // if($update) {
                        //     $arguments['--update']  = true;
                        // }
                        // if($output->isVerbose()) {
                        //     $arguments['--verbose'] = true;
                        // }

                        // $returnCode = $command->run(new ArrayInput($arguments), $output);
                        // if($returnCode == 0) {
                        //     $this->notice('Subcommand processed successfully', $arguments);
                        // }
                        // else {
                        //     $this->error('Subcommand failed', $arguments);
                        // }
                    }
                    $return_code = 2;
                } else {
                    if ($project->one_round) {
                        if(empty($project->success)) {
                            $action_done = true;
                            // one round only project
                            $this->notice('Ending round for one-round-only project', [$project, 'project_days_round1' => $project->days_round1]);
                            if ($update) {
                                // dispatch passing event, will generate a feed entry if needed
                                $project = $this->dispatch(ConsoleEvents::PROJECT_ONE_ROUND, new FilterProjectEvent($project))->getProject();
                            }
                        }
                    } elseif ($project->days_active >= $project->days_total) {
                        // 2 rounds project, end of life
                        $action_done = true;
                        $this->notice('Ending second round for 2-rounds project', [$project, 'project_days_active' => $project->days_active, 'project_days_total' => $project->days_total]);
                        if ($update) {
                            // dispatch passing event, will generate a feed entry if needed
                            $project = $this->dispatch(ConsoleEvents::PROJECT_ROUND2, new FilterProjectEvent($project))->getProject();
                        }

                    } elseif (empty($project->passed)) {
                        // 2 rounds project, 1srt round passed
                        $action_done = true;
						$this->notice('Ending first round for 2-rounds project', [$project, 'project_days_round1' => $project->days_round1, 'project_days_total' => $project->days_total]);
						if ($update) {
							// dispatch passing event, will generate a feed entry if needed
							$project = $this->dispatch(ConsoleEvents::PROJECT_ROUND1, new FilterProjectEvent($project))->getProject();
						}

					} else {
						// este caso es lo normal estando en segunda ronda
						$this->debug('Project in second round, still active', [$project, 'project_days_round1' => $project->days_round1, 'project_days_round2' => $project->days_round2, 'project_percent' => $percent]);
					}

				}
			}

			$processed++;
		}

        if (!$action_done) {
            $this->info("No actions required");
        }
        if ($processed == 0) {
			$this->info("No projects processed");
			return;
		}

		if (!$update) {
			$this->warning('Dummy execution. No write operations done');
			$output->writeln('<comment>No write operations done. Please execute the command with the --update modifier to perform write operations</comment>');
		}
        return $return_code;
	}
}
