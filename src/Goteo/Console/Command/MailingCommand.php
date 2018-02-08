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

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterMailingEvent;

use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class MailingCommand extends AbstractCommand {

	protected function configure() {
		$this->setName("mailing")
		     ->setDescription("Send massive mail")
		     ->setDefinition(array(
				new InputArgument('mailer_id', InputArgument::REQUIRED, 'ID from the mailer_content table. Sends a massive sending'),
				new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
				new InputOption('force', 'f', InputOption::VALUE_NONE, 'Does not check for the message or mailing to be unlocked before sending'),
			))
		->setHelp(<<<EOT
This script processes pending massive mailing and sends it
Massive mailing will throw a background process

Usage:

Processes pending mailing
<info>./console mailing</info>

Sends the last pending massive mailing from the mailer_content table:
<info>./console mailing last</info>

Sends the 12345 pending massive mailing from the mailer_content table:
<info>./console mailing 12345</info>

EOT
		)

		;

	}

	/**
	 * Massive handling
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$update  = $input->getOption('update');
		$massive = $input->getArgument('mailer_id');
		$force   = $input->getOption('force');

		// cogemos el siguiente envío a tratar
		$mailing = Sender::get($massive);
		if (!$mailing instanceOf Sender) {
			throw new ModelNotFoundException("ID [$massive] not found in mailer_content table!");
		}

		// Limite para sender, (deja margen para envios individuales)
        $LIMIT = Config::get('mail.quota.sender', true);
        $SEND_RATE = Config::get('mail.quota.send_rate', true);
		$CONCURRENCY = Config::get('mail.quota.concurrency', true);

		// check the limit
		if ($LIMIT && !Mail::checkLimit(null, false, $LIMIT)) {
			// throw new \RuntimeException("Quota exceeded for today", [$mailing, 'quota' => $LIMIT]);
			$mailing = $this->dispatch(ConsoleEvents::MAILING_ABORTED, new FilterMailingEvent($mailing, "Quota exceeded for today"))->getSender();
		}

		if (!$mailing->active) {
            if($massive) {
                $this->debug("No mailing to send", ['quota' => $LIMIT]);
                return;
            }
			$this->warning("Mailing is inactive. ABORTING SENDING", [$mailing, 'quota' => $LIMIT]);
			return;
		}

		if (!$force && !$mailing->blocked) {
			// Double check blocking, just in case
			$mailing = Sender::get($mailing->id);
		}

		if ($mailing->blocked) {
			$this->error("Mailing is blocked. ABORTING SENDING", [$mailing, 'quota' => $LIMIT]);
            return;
        }

        // Total receivers
        $total_users   = $mailing->getRecipients(0, 0, true);
        if($total_users == 0) {
            $this->error("Mailing has no recipients. ABORTING SENDING", [$mailing, 'quota' => $LIMIT]);
			return;
        }


        if (!$force) {
            $this->info("Locking massive mailing", [$mailing, 'quota' => $LIMIT]);
            if (!$mailing->setLock(true)->blocked) {
                throw new \RuntimeException("Error locking mailing [{$mailing->id}]");
            }
        }


		$total_pending = $mailing->getPendingRecipients(0, 0, true);

		// none pending, sends the finished event
		if ($total_pending == 0 && $update) {
			$mailing = $this->dispatch(ConsoleEvents::MAILING_FINISHED, new FilterMailingEvent($mailing))->getSender();
		} else {
			$this->notice("Sending massive mailing", [$mailing, 'total_pending' => $total_pending, 'total_users' => $total_users, 'quota' => $LIMIT]);

			try {
				$itime               = microtime(true);
				$current_rate        = 0;
				$current_concurrency = $increment = 2;
				$iterations          = 0;
				$php                 = (new PhpExecutableFinder())->find();

				// Load recipients in blocks
				// Lock temporary
				while ($users = $mailing->getPendingRecipients(0, $current_concurrency, false, true)) {
					// check the limit
					if ($LIMIT && !Mail::checkLimit(null, false, $LIMIT)) {
						throw new \RuntimeException("Quota exceeded for today");
					}

					$processes = [];
					$stime     = microtime(true);
					foreach ($users as $recipient) {
						$recipient->setLock(false);
						$line = $php.' '.GOTEO_PATH."bin/console sendmail {$recipient->id} --lock --lock-name sendmail.{$recipient->id}" .($update?' --update':'');
						//. ' --verbose  2>/dev/null';
						$process = new Process($line);
                        $process->setTimeout(null); // no time limit
						$process->start();
						$pid             = $process->getPid();
						$processes[$pid] = $process;
					}

					$this->debug('Waiting for processes to end', ['pids' => array_keys($processes)]);

					// wait for processes to end
					$that = $this;
					foreach ($processes as $pid => $process) {
						$code = $process->wait(function ($type, $buffer) use ($output, $pid) {
								$output->write("[PID $pid] " .$buffer);
								// if (Process::ERR === $type) {
								//     $output->write($buffer);
								// }
							});
						// collision returns code 13
						if ($code == 0) {
							$iterations++;
						}
					}

					if ($iterations > $total_users) {
						throw new \RuntimeException("Iterations [$iterations] over maximum total users [$total_users]");
					}

					$process_time = microtime(true)-$stime;
					$current_rate = round($j/$process_time, 2);
					$this->info("Quota left for today: [$rest] emails, Quota limit: [$LIMIT]");
					$this->info("Rate sending (per second): $current_rate - Rate limit: [$SEND_RATE]");

					//aumentamos la concurrencia si el ratio es menor que el 75% de máximo
					if ($current_rate < $SEND_RATE*0.75 && $current_concurrency < $CONCURRENCY) {
						$current_concurrency += 2;
						$this->debug("Ratio less than 75% from maximum, raising concurrency to [$current_concurrency]");
					}

					//disminuimos la concurrencia si llegamos al 90% del ratio máximo
					if ($current_rate > $SEND_RATE*0.9) {
						$wait_time = ceil($current_rate - $SEND_RATE*0.9);
						$current_concurrency--;
						$this->debug("Ratio overpassing 90% from maximum, waiting [$wait_time] seconds, lowering concurrency to [$current_concurrency]");
						sleep($wait_time);
					}

					$total_pending = $mailing->getPendingRecipients(0, 0, true);
					// echo "NEW PENDING $total_pending\n";
					if ($total_pending == 0) {
						$this->notice("END. Total execution time [".round(microtime(true)-$itime, 2)."] seconds $total_users emails, Medium rate [" .round($total_users/(microtime(true)-$itime), 2)."] emails/second");
						if ($update) {
							$mailing = $this->dispatch(ConsoleEvents::MAILING_FINISHED, new FilterMailingEvent($mailing))->getSender();
						}
						break;
					}
				}

			} catch (\Exception $e) {
				$mailing = $this->dispatch(ConsoleEvents::MAILING_ABORTED, new FilterMailingEvent($mailing, $e->getMessage()))->getSender();
				throw $e;
			}
		}

		if (!$force) {
			$this->info("Unlocking massive mailing", [$mailing, 'quota' => $LIMIT]);
			if ($mailing->setLock(false)->blocked) {
				throw new \RuntimeException("Error unlocking mailing [{$mailing->id}]");
			}
		}

	}
}
