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
use Goteo\Model\Project;

class StatusInitCommand extends AbstractCommand {
    protected $output;

    protected function configure()
    {
        $this->setName("dev:statusinit")
             ->setDescription("Initializes the database with some know project status")
              ->setDefinition(array(
                       new InputOption('create', 'c', InputOption::VALUE_NONE, 'Creates the initial status using the current date as a reference'),
                       new InputOption('erase', 'e', InputOption::VALUE_NONE, 'Deletes all the data inserted by the inicial status'),
                       new InputOption('delta', 'd', InputOption::VALUE_REQUIRED, 'Updates the dates of publish, success, closed by this number of days in the past', 0),
                 ))
             ->setHelp(<<<EOT
This script initializes projects, invests and users to some known status in order to allow tests

Usage:

Run the SQL scripts to initialize status
<info>./console dev:statusinit</info>

Run the SQL scripts to initialize status with published, passed and closed dates
so it can be used to test the endround (and others such as projectwatch) command

One day succeeded, published or failed projects:
<info>./console dev:statusinit --delta 1</info>

Ten days succeeded, published or failed projects:
<info>./console dev:statusinit --delta 10</info>

Removes all testing data:
<info>./console dev:statusinit --erase</info>

Removes all testing data and creates a fresh one:
<info>./console dev:statusinit -ec</info>

Removes all testing data and creates a fresh one 5 days in the past:
<info>./console dev:statusinit -ecd 5</info>

Be verbose (show the SQL executed):
<info>./console dev:statusinit --erase -v</info>
EOT
);
    }

    protected function query($sql) {
        $lines = explode("\n", $sql);
        $queries = [];
        $query = '';
        foreach($lines as $line) {
            if(strpos(ltrim($line), 'INSERT INTO') === 0 || strpos(ltrim($line), 'UPDATE') === 0 || strpos(ltrim($line), 'DELETE') === 0) {
                if($query) $queries[] = $query;
                $query = "$line\n";
            }
            elseif($query) {
                $query .= "$line\n";
            }
        }
        if($query) $queries[] = $query;
        foreach($queries as $s) {
            if($this->output->isVerbose()) {
                $this->output->writeln("Executing <info>$s</info>");
            }
            $res = Model::query($s);
            $res->closeCursor();
        }
        return $res;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $erase  = $input->getOption('erase');
        $delta  = $input->getOption('delta');
        $create  = $input->getOption('create');
        if(empty($erase) && empty($delta) && empty($create)) {
            throw new \Exception("Please specify one or more options to run this command. use --help for more information");
        }
        $this->output = $output;
        if($erase)
        {
            $output->writeln("<comment>Deleting projects creating by initial status</comment>");

            //Deleting project finishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_finishing_today.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 1 deleted: <info>project-finishing-today</info>");

            //Deleting project publishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_publishing_today.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 1 deleted: <info>project-publishing-today</info>");

            //Deleting project passing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_passing_today.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 2 deleted: <info>project-passing-today</info>");

            //Deleting one round project finishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_one_round_finishing_today.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 3 deleted: <info>project-one-round-finishing</info>");

            //Deleting failed project finishing today and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_failed_finishing_today.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 4 deleted: <info>project-failed-finishing-today</info>");

            //Deleting project finishing in five days and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_finishing_five_days.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 5 deleted: <info>project-finishing-five-days</info>");

            //Deleting project passed yesterday and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_passed_yesterday.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 6 deleted: <info>project-passed-yesterday</info>");

            //Deleting one round project finished yesterday and related
            $filename = GOTEO_PATH.'db/tests-data/projects-data/delete_one_round_finished_yesterday.sql';
            $sql = file_get_contents($filename);
            $this->query($sql);

            $output->writeln("Project 7 deleted: <info>project-one-round-finished</info>");

        }
        if($create)
        {
            try {
                $output->writeln("<comment>Creating projects with diferents status</comment>");

                //Project finishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/finishing_today.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 1 created ID: <info>project-finishing-today</info>");

                //Project publishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/publishing_today.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 1 created ID: <info>project-publishing-today</info>");

                //Project passing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/passing_first_round_today.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 2 created ID: <info>project-passing-today</info>");

                //Project one round finishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/one_round_finishing.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 3 created ID: <info>project-one-round-finishing</info>");

                 //Project failed finishing today
                $filename = GOTEO_PATH.'db/tests-data/projects-data/failed_finishing_today.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 4 created ID: <info>project-failed-finishing-today</info>");

                //Project finishing five days
                $filename = GOTEO_PATH.'db/tests-data/projects-data/finishing_five_days.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("<comment>Project 5 created ID: <info>project-finishing-five-days</comment>");

                //Project passed yesterday
                $filename = GOTEO_PATH.'db/tests-data/projects-data/passed_first_round_yesterday.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 6 created ID: <info>project-passed-yesterday</info>");


                //Project one round finished yesterday
                $filename = GOTEO_PATH.'db/tests-data/projects-data/one_round_finished_yesterday.sql';
                $sql = file_get_contents($filename);
                $this->query($sql);

                $output->writeln("Project 7 created ID: <info>project-one-round-finished</info>");

            }
            catch(\PDOException $e) {
                $output->writeln("<error>Error creating tables:</error> <fg=red>" . $e->getMessage() .'</>');
            }

            if($delta)
            {
                $output->writeln("<comment>Putting projects in the past by $delta days</comment>");
                //Deleting project passing today and related
                $filename = GOTEO_PATH.'db/tests-data/projects-data/delta.sql';
                $sql = file_get_contents($filename);

                $sql = str_replace(['%DELTA%', '%STATUS_SUCCESS%', '%STATUS_FAILED%', '%STATUS_ACTIVE%'], [$delta, Project::STATUS_FUNDED, Project::STATUS_UNFUNDED, Project::STATUS_IN_CAMPAIGN], $sql);
                $this->query($sql);
                $output->writeln("<info>Changed project dates using</info> <fg=red>" . date("Y-m-d", mktime(0,0,0,date('m'),date('d')-$delta, date('Y'))) . "</> <info>as today</info>");
            }


        }

     }
}
