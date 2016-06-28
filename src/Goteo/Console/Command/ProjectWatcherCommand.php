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

use Goteo\Console\Event\FilterProjectEvent;
use Goteo\Console\ConsoleEvents;
use Goteo\Model\Project;

/**
 * Proceso para enviar avisos a los autores segun
 *  - Que lleven 3 meses sin publicar actualizaciones, envia cada mes
 *  - Que lleven 3 meses sin decir nada (?), envia cada 15 dias
 *  - Que hayan pasado dos meses desde que se dio por financiado, cada 15 dias
 *
 *  tiene en cuenta que se envía cada tantos días
 *  CRON SUGGESTED LINE:
 *  5 3 * * *       www-data        /usr/bin/php /..path.../bin/console projectwatch --update  > /..path.../var/logs/last-cli-projectwatch.log
 */
class ProjectWatcherCommand extends AbstractCommand {

    protected function configure()
    {
        // Old command, old notice hidding
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

        $this->setName("projectwatch")
             ->setDescription("Throws events for projects (used to send advises to owners for example)")
             ->setDefinition(array(
                new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                new InputOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Only processes the specified Project ID'),
            ))

             ->setHelp(<<<EOT
This script throws events during the active live of a project

Events are:
- Active project
- Watched project
- Ending project

Usage:

Use the --update to actually thrown the events
<info>./console projectwatch [--update]</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update       = $input->getOption('update');
        $project_id   = $input->getOption('project');

        $filter = [];
        if ($project_id) {
            $output->writeln("<info>Processing Project [$project_id]:</info>");
            $filter = ['proj_id' => $project_id];
        }

        $processed = 0;

        $total = Project::getList($filter + ['status' => Project::STATUS_REVIEWING, 'published' => date('Y-m-d')], null, 0, 0, true);
        $output->writeln("Found <info>$total projects</info> for publishing today");
        foreach(Project::getList($filter + ['status' => Project::STATUS_REVIEWING, 'published' => date('Y-m-d')], null, 0, $total) as $prj) {
            // PUBLISH EVENT
            $event = new FilterProjectEvent($prj);
            $output->writeln("Throwing publish project event for <info>{$prj->id}</info>, published <comment>{$prj->published}</comment>");
            if ($update) {
                $this->dispatch(ConsoleEvents::PROJECT_PUBLISH, $event);
                $processed ++;
            }
        }

        $total = Project::getList($filter + ['status' => Project::STATUS_IN_CAMPAIGN], null, 0, 0, true);
        $output->writeln("Found <info>$total projects</info> IN CAMPAIGN");
        foreach(Project::getList($filter + ['status' => Project::STATUS_IN_CAMPAIGN], null, 0, $total) as $prj) {

            // a los 5, 3, 2, y 1 dia para finalizar ronda
            if ($prj->round > 0 && in_array((int) $prj->days, array(5, 3, 2, 1))) {
                $output->writeln("Throwing round ending event due remaining {$prj->days} days until end of round {$prj->round} for <info>{$prj->id}</info>, published <comment>{$prj->published}</comment>");
                $action_done = true;
                if ($update) {
                    // dispatch ending event, will generate a feed entry if needed
                    $prj = $this->dispatch(ConsoleEvents::PROJECT_ENDING, new FilterProjectEvent($prj))->getProject();
                    $processed ++;
                }
            }

            // ACTIVE EVENT
            $event = new FilterProjectEvent($prj);
            $output->writeln("Throwing active project event for <info>{$prj->id}</info>, published <comment>{$prj->published}</comment>, active days <comment>" . $event->getDays() . "</comment>, funded days <comment>" . $event->getDaysFunded() . "</comment>, succeeded days <comment>" . $event->getDaysSucceeded() . "</comment>");
            if ($update) {
                $this->dispatch(ConsoleEvents::PROJECT_ACTIVE, $event);
                $processed ++;
            }

        }

        $min_date = date("Y-m-d", mktime(0, 0, 0, date('m'), date('d'), date('Y') - 1));

        $total = Project::getList($filter + ['status' => Project::STATUS_FUNDED, 'succeeded_since' => $min_date], null, 0, 0, true);
        $output->writeln("Found <info>$total projects</info> succeeded since <comment>$min_date</comment>");
        foreach(Project::getList($filter + ['status' => Project::STATUS_FUNDED, 'succeeded_since' => $min_date], null, 0, $total) as $prj) {

            // WATCH EVENT
            $event = new FilterProjectEvent($prj);

            $output->writeln("Throwing watch project event for <info>{$prj->id}</info>, published <comment>{$prj->published}</comment>, active days <comment>" . $event->getDays() . "</comment>, funded days <comment>" . $event->getDaysFunded() . "</comment>, succeeded days <comment>" . $event->getDaysSucceeded() . "</comment>");

            if ($update) {
                $this->dispatch(ConsoleEvents::PROJECT_WATCH, $event);
                $processed ++;
            }

        }

        $output->writeln("<info>$processed events thrown</info>");

        if (!$update) {
            $this->warning('<error>Dummy execution. No events thrown</error>');
            $output->writeln('<comment>Please execute the command with the --update modifier to exectute the events</comment>');
        }

    }
}
