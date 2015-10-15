<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Omnipay\Common\Message\ResponseInterface;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Goteo\Util\Monolog\Handler\MailHandler;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\IntrospectionProcessor;

use Goteo\Application\Config;
use Goteo\Application\App;
use Goteo\Application\Event\FilterInvestRefundEvent;
use Goteo\Application\AppEvents;
use Goteo\Model\Mail;
use Goteo\Model\Invest;
use Goteo\Model\User;
use Goteo\Payment\Payment;

// TODO: ContainerAware like
class RoundExecuteCommand extends Command {

    protected function configure()
    {
        $this->setName("execute")
             ->setDescription("Processes payments and refunds (1st & 2nd rounds)")
             ->setDefinition(array(
                      new InputOption('test', 't', InputOption::VALUE_NONE, 'Do a fail test and send a failure mail'),
                      new InputOption('archive', 'a', InputOption::VALUE_NONE, 'Processes refund for payments no yet refunded for archived projects'),
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done'),
                ))
             ->setHelp(<<<EOT
This script proccesses payments when active projects reaches ending rounds.
A failed project will refund payments to the backers.
A successful project will process payments (if required) and his status changed.

Usage:

Test a failure operation (mail sending)
<info>./console execute --test</info>

Processes pending refunds for archived (failed) projects in read-only mode
<info>./console execute --archive</info>

Processes pending refunds for archived (failed) projects and write operations to database
<info>./console execute --archive --update</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = Config::get('env');
        $log = new Logger('execute', [
                        new StreamHandler('php://stderr', Logger::INFO),
                        new RotatingFileHandler(GOTEO_LOG_PATH . "execute_$env.log", 0, Logger::DEBUG),
                        new MailHandler(Mail::createFromHtml(Config::getMail('fail'),
                                                             '',
                                                             "ERROR: [round-execute] in [" . Config::get('url.main') . "] ",
                                                             "<pre>SERVER: " . print_R($_SERVER, 1) . "</pre>\n"
                                                             ),
                                        Logger::ERROR)
                    ], [
                        // processor, memory usage
                        new MemoryUsageProcessor,
                        new IntrospectionProcessor(Logger::ERROR)
                    ]);

        $test  = $input->getOption('test');
        $archive  = $input->getOption('archive');
        $update  = $input->getOption('update');
        try {
            if ( $test ) {
               $output->writeln('<comment>Throwing some errors intentionally to test email sending</comment>');
               throw new \Exception('This is a simulated failed execution!');
            }
            elseif( $archive ) {
               $output->writeln('<comment>Showing projects archived with pending refunds:</comment>');
                if($invests = Invest::getFailed()) {
                    $processed = 0;
                    foreach($invests as $invest) {
                        if(!Payment::methodExists($invest->method)) {
                            $log->debug("SKIPPING NON EXISTING METHOD: {$invest->method} from INVEST: {$invest->id} STATUS: {$invest->status}");
                            if ($output->isVerbose()) {
                                $output->writeln("<comment>SKIPPING METHOD: {$invest->method} from INVEST: {$invest->id} STATUS: {$invest->status}</comment>");
                            }
                            continue;
                        }
                        //retorna el dinero
                        // print_r($invest);
                        $log->info("Found active invest on archived project: {$invest->id} STATUS: {$invest->status} METHOD: {$invest->method} INVESTED: {$invest->invested} PROJECT: {$invest->project} USER: {$invest->user} PREAPPROVAL: {$invest->preapproval}");
                        if( $update ) {
                            $method = Payment::getMethod($invest->method);
                            $method->setUser(User::get($invest->user));
                            $method->setInvest($invest);
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
                                $invest = App::dispatch(AppEvents::INVEST_RETURNED, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
                                // New Invest Refund Event
                                if($invest->status === Invest::STATUS_RETURNED) {
                                    $log->info('Invest cancelled successfully');
                                    $output->writeln('<info>Invest cancelled successfully</info> ' . $response->getMessage());
                                } else {
                                    $log->error('Error cancelling invest. INVEST:' . $invest->id . ' STATUS: ' . $invest->status);
                                    $output->writeln('<error>Invest not cancelled!</error>' .  $response->getMessage());
                                }
                            }
                            else {
                                $invest = App::dispatch(AppEvents::INVEST_RETURN_FAILED, new FilterInvestRefundEvent($invest, $method, $response))->getInvest();
                                $log->error('Error returning invest. INVEST:' . $invest->id . ' STATUS: ' . $invest->status);
                                $output->writeln('<error>Failed return for invest!</error> ' . $response->getMessage());
                            }

                        }
                        $processed++;
                    }
                }
                if($processed == 0) {
                    $log->info("No failed invests found");
                    $output->writeln('<info>--No errors found--</info>');
                }

            }
            else {
                $output->writeln("<comment>Please run this command with options. Use --help for more info</comment>");
                return;
            }
            if( !$update ) {
               $log->warning('Dummy execution. No write operations done');
               $output->writeln('<comment>No write operations done. Please execute the command with the --update modifier to perform write operations</comment>');
            }
        }
        catch(\Exception $e) {
            $log->error($e->getMessage());
            $output->writeln("<error>PROCESS FAILED: " . $e->getMessage() . "</error>");
        }


    }
}
