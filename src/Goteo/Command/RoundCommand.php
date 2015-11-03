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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


class RoundCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("round")
             ->setDescription("Project status changer for 1st and 2on round")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                ))
             ->setHelp(<<<EOT
This script proccesses active projects reaching ending rounds.
A failed project will change his status to failed.
A successful project which reached his first round will start a second round.
A successful project which reached his second round will end his invest life time.

Usage:

Processes pending projects in read-only mode
<info>./console round</info>

Processes pending projects and write operations to database
<info>./console round --update</info>


EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
           $output->writeln('<comment>nothing yet</comment>');
        }
        catch(\Exception $e) {
            $this->error($e->getMessage());
            $output->writeln("<error>PROCESS FAILED: " . $e->getMessage() . "</error>");
        }


    }
}
