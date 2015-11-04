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


class TestCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("test")
             ->setDescription("A mail testing command")
             ->setDefinition(array(
                      new InputOption('fail', 'f', InputOption::VALUE_NONE, 'Do a fail test and send a failure mail'),
                ))
             ->setHelp(<<<EOT
This script throws an exception in order to check Mail sending behaviour

Usage:

Test a failure operation (sends a mail)
<info>./console test --fail</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ( $input->getOption('fail') ) {
           $output->writeln('<comment>Throwing some errors intentionally to test email sending</comment>');
           $this->error('Simulated error log line');
           throw new \Exception('This is a simulated failed execution!');
        }
        else {
            $output->writeln("<comment>Please run this command with options. Use --help for more info</comment>");
            return;
        }
    }
}
