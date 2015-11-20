<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteodev\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Goteo\Console\Command\AbstractCommand;
use Goteo\Core\Model;
use Goteo\Model\Invest;

class StatusInitCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("dev:statusinit")
             ->setDescription("Initializes the database with some know project status")
              ->setDefinition(array(
                       new InputOption('clean', 'c', InputOption::VALUE_NONE, 'Clean the data inserted by the inicial status'),
                       new InputOption('expired', 'e', InputOption::VALUE_REQUIRED, 'Sets test ending rounds as already expired', 0),
                 ))
             ->setHelp(<<<EOT
This script initializes projects, invests and users to some known status in order to allow tests

Usage:

Run the SQL scripts to initialize status
<info>./console dev:statusinit</info>

Run the SQL scripts to initialize status with expired dates
so it can be used to test endround command
Specify num fo days the project are expired

One day expired:
<info>./console dev:statusinit --expired 1</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $clean  = $input->getOption('clean');

        if($clean)
        {
            $output->writeln("<comment>Deleting projects creating by initial status</comment>");

            //Deleting project finishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_finishing_today.sql';
            $sql = file_get_contents($filename);
            Model::query($sql);

            $output->writeln("<comment>Project 1 deleted: 'project-finishing-today'</comment>");

            //Deleting project passing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_passing_today.sql';
            $sql = file_get_contents($filename);
            Model::query($sql);

            $output->writeln("<comment>Project 2 deleted: 'project-passing-today'</comment>");

            //Deleting one round project finishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_one_round_finishing_today.sql';
            $sql = file_get_contents($filename);
            Model::query($sql);

            $output->writeln("<comment>Project 3 deleted: 'project-one-round-finishing'</comment>");

            //Deleting failed project finishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_failed_finishing_today.sql';
            $sql = file_get_contents($filename);
            Model::query($sql);

            $output->writeln("<comment>Project 4 deleted: 'project-failed-finishing-today'</comment>");

            //Deleting project finishing in five days and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_finishing_five_days.sql';
            $sql = file_get_contents($filename);
            Model::query($sql);

            $output->writeln("<comment>Project 5 deleted: 'project-finishing-five-days'</comment>");

        }
        else
        {
            try {
                $output->writeln("<comment>Creating projects with diferents status</comment>");

                //Project finishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/finishing_today.sql';
                $sql = file_get_contents($filename);
                Model::query($sql);

                $output->writeln("<comment>Project 1 created ID: 'project-finishing-today'</comment>");

                //Project passing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/passing_first_round_today.sql';
                $sql = file_get_contents($filename);
                Model::query($sql);

                $output->writeln("<comment>Project 2 created ID: 'project-passing-today'</comment>");

                //Project one round finishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/one_round_finishing.sql';
                $sql = file_get_contents($filename);
                Model::query($sql);

                $output->writeln("<comment>Project 3 created ID: 'project-one-round-finishing'</comment>");

                 //Project failed finishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/failed_finishing_today.sql';
                $sql = file_get_contents($filename);
                Model::query($sql);

                $output->writeln("<comment>Project 4 created ID: 'project-failed-finishing-today'</comment>");

                //Project finishing five days
                $filename = GOTEO_PATH.'db/tests-data/projects-data/finishing_five_days.sql';
                $sql = file_get_contents($filename);
                Model::query($sql);

                $output->writeln("<comment>Project 5 created ID: 'project-finishing-five-days'</comment>");
            }
            catch(\PDOException $e) {
                $output->writeln("<error>Error creating tables:</error> <fg=red>" . $e->getMessage() .'</>');
            }

            $expired = (int) $input->getOption('expired');
            if($expired)
            {
                //Deleting project passing today and related
                $filename = GOTEO_PATH.'db/tests-data/projects-data/expired.sql';
                $sql = file_get_contents($filename);
                $sql = str_replace(['%DAYS_1_ROUND%', '%DAYS_2_ROUND%'], [39 + $expired, 79 + $expired], $sql);
                Model::query($sql);
                $output->writeln("<comment>Changed project to expired date: " . date("Y-m-d", mktime(0,0,0,date('m'),date('d')-$expired, date('Y'))) . "</comment>");
            }


        }


        /*
        $output->writeln("<info>a hard work</info>");
        $output->writeln("<error>in case of error</error>");
        */
     }
}
