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

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Goteo\Util\Monolog\Handler\MailHandler;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\IntrospectionProcessor;

use Goteo\Application\Config;
use Goteo\Model\Mail;

class RoundExecuteCommand extends Command {

    protected function configure()
    {
        $this->setName("goteo:execute")
             ->setDescription("Processes payments and refunds (1st & 2nd rounds)")
             ->setDefinition(array(
                      new InputOption('test', 't', InputOption::VALUE_NONE, 'Do a fail test and send a failure mail'),
                ))
             ->setHelp(<<<EOT
This script gets active projects and process rounds

Usage:

Test a failure operation (mail sending)
<info>./console goteo:execute --test</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $env = Config::get('env');
        $log = new Logger('execute', [
                        new StreamHandler('php://stderr', Logger::DEBUG),
                        new RotatingFileHandler(GOTEO_LOG_PATH . "execute_$env.log", 0, Logger::INFO),
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
        try {
            if ( $test ) {
               throw new \Exception('This is a simulated failed execution!');
            }
        }
        catch(\Exception $e) {
            $log->error($e->getMessage());
        }


    }
}
