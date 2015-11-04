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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Omnipay\Common\Message\ResponseInterface;

use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Console\ConsoleEvents;
use Goteo\Model\Mail;
use Goteo\Model\Invest;
use Goteo\Payment\Payment;

class RefundCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("refund")
             ->setDescription("Processes refunds for failed projects")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                ))
             ->setHelp(<<<EOT
This script proccesses refunds for payments no yet refunded for archived projects
A failed project will refund payments to the backers.

Usage:

Processes pending refunds for archived (failed) projects in read-only mode
<info>./console refund</info>

Processes pending refunds for archived (failed) projects and write operations to database
<info>./console refund --update</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update  = $input->getOption('update');

        $output->writeln('<comment>Showing projects archived with pending refunds:</comment>');
        if($invests = Invest::getFailed()) {
            $processed = 0;
            foreach($invests as $invest) {
                if(!Payment::methodExists($invest->method)) {
                    $this->debug("SKIPPING NON EXISTING METHOD: {$invest->method} from INVEST: {$invest->id} STATUS: {$invest->status}");
                    if ($output->isVerbose()) {
                        $output->writeln("<comment>SKIPPING METHOD: {$invest->method} from INVEST: {$invest->id} STATUS: {$invest->status}</comment>");
                    }
                    continue;
                }

                $this->info("Found active invest on archived project: {$invest->id} STATUS: {$invest->status} METHOD: {$invest->method} INVESTED: {$invest->invested} PROJECT: {$invest->project} USER: {$invest->user} PREAPPROVAL: {$invest->preapproval}");

                // get my money back!!
                if( $update ) {
                    $method = $invest->getMethod();
                    // process gateway refund
                    // go to the gateway, gets the response
                    $response = $method->refund();

                    // Checks and redirects
                    if (!$response instanceof ResponseInterface) {
                        throw new \RuntimeException('This response does not implements ResponseInterface.');
                    }

                    // On-sites can return a successful response here
                    if ($response->isSuccessful()) {
                        // Event invest success
                        $invest = $this->dispatch(ConsoleEvents::INVEST_RETURNED, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
                        // New Invest Refund Event
                        if($invest->status === Invest::STATUS_RETURNED) {
                            $this->info('Invest cancelled successfully');
                            $output->writeln('<info>Invest cancelled successfully</info> ' . $response->getMessage());
                        } else {
                            $this->error('Error cancelling invest. INVEST: ' . $invest->id . ' STATUS: ' . $invest->status);
                            $output->writeln('<error>Invest not cancelled!</error>' .  $response->getMessage());
                        }
                    }
                    else {
                        $invest = $this->dispatch(ConsoleEvents::INVEST_RETURN_FAILED, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
                        $this->error('Error refunding invest. INVEST: ' . $invest->id . ' STATUS: ' . $invest->status);
                        $output->writeln('<error>Failed return for invest!</error> ' . $response->getMessage());
                    }

                }
                $processed++;
            }
        }
        if($processed == 0) {
            $this->info("No failed invests found");
            $output->writeln('<info>--No errors found--</info>');
        }

        if( !$update ) {
           $this->warn('Dummy execution. No write operations done');
           $output->writeln('<comment>No write operations done. Please execute the command with the --update modifier to perform write operations</comment>');
        }


    }
}
