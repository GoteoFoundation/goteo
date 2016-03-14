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

use Goteo\Model\Project;
use Goteo\Model\Template;

/**
 *  Proceso que verifica si los preapprovals han sido coancelados
 *  Solamente trata transacciones paypal pendientes de proyectos en campaña
 *
 *  CRON SUGGESTED LINE:
 *  5 3 * * *       www-data        /usr/bin/php /..path.../bin/console dbverify --update  > /..path.../var/logs/last-cli-dbverify.log
 */
class DBVerifierCommand extends AbstractCommand {

    protected function configure()
    {
        // Old command, old notice hidding
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

        $this->setName("dbverify")
             ->setDescription("Database cleaner")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the cleaning action, read-only operation otherwise'),
                      new InputOption('days', 'd', InputOption::VALUE_REQUIRED, 'Number of days that a record is considered "old" (120 by default)', -1),
                      new InputOption('scope', 's', InputOption::VALUE_REQUIRED, 'Optional operation scope (default all): [all|feed|token|mailing|blocked]', 'all')
                ))

             ->setHelp(<<<EOT
This script cleans old records from several tables:
- mail table
- feed table
Also removes old generated tokens (by password change for example)

Usage:

User the --update option to actually make changes to the database
<info>./console dbverify [--update]</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');
        $scope = $input->getOption('scope');
        if(!in_array($scope, ['all', 'feed', 'mailing', 'token', 'blocked'])) {
            throw new \Exception('Scope is not valid!');
        }


        $verbose = $output->isVerbose();

        if(in_array($scope, ['all', 'feed'])) {
            $days = (int) $input->getOption('days');
            if($days == -1) $days = 120;
            if($days < 30) {
                throw new \Exception('Number of days must be greater than 30!');
            }
            $index = $fixes = 0;
            $output->writeln("Checking old feed data...");
            $where = "WHERE type != 'goteo'
                    AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`datetime`)), '%j') > $days
                    AND (url NOT LIKE '%updates%' OR url IS NULL)";

            if($verbose) {
                $query = Project::query("SELECT * FROM feed $where");

                foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $feed) {
                    $output->writeln("Found old feed <info>{$feed->title}</info> with ID <comment>{$feed->id}</comment> and date <comment>{$feed->datetime}</comment>");
                }
            }
            $query = Project::query("SELECT count(*) as total FROM feed");
            $total = $query->fetchColumn();
            $query = Project::query("SELECT count(*) as total FROM feed $where");
            $found = $query->fetchColumn();
            $output->writeln("Found <comment>$found</comment> <info>feed records</info> older thant <comment>$days days</comment> from a total of <comment>$total</comment> records");
            $index += $found;

            if($update) {
                $query = Project::query("DELETE FROM feed $where");
                $fixes += $query->rowCount();
            }
        }

        if(in_array($scope, ['all', 'token'])) {
            $days = (int) $input->getOption('days');
            if($days == -1) $days = 4;
            if($days < 1) {
                throw new \Exception('Number of days must be greater than 2!');
            }

            $output->writeln("Checking token older thant $days days...");
            // eliminamos los tokens que tengan más de $days días
            $sql = "SELECT id, token FROM user WHERE token IS NOT NULL AND token != '' AND token LIKE '%¬%'";
            $query = Project::query($sql);
            foreach ($query->fetchAll(\PDO::FETCH_OBJ) as $row) {
                $parts = explode('¬', $row->token);
                $datepart = strtotime($parts[2]);
                $today = date('Y-m-d');
                $datedif = strtotime($today) - $datepart;
                $d = round($datedif / 86400);
                if ($d > $days || !isset($parts[2])) {
                    $output->writeln("User: <info>{$row->id}</info> Token: <comment>{$row->token}</comment> Date: <comment>{$parts[2]}</comment> Days: <comment>$d</comment>");

                    $index++;
                    if($update) {
                        if(Project::query("UPDATE user SET token = '' WHERE id = ?", array($row->id))) {
                            $output->writeln("Token {$row->token} cleaned");
                            $fixes ++;
                        }
                    }
                }

            }
        }

        if(in_array($scope, ['all', 'mailing'])) {
            $days = (int) $input->getOption('days');
            if($days == -1) $days = 120;
            if($days < 30) {
                throw new \Exception('Number of days must be greater than 90!');
            }

            $output->writeln("Checking old mail data...");
            $where = " WHERE (template != :template OR template IS NULL)
                        AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`date`)), '%j') > $days";

            $query = Project::query("SELECT * FROM mail $where", [':template' => Template::NEWSLETTER]);

            $found = 0;
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $mail) {
                if($verbose) {
                    $output->writeln("Found old mail <info>{$mail->email}</info>, Template <comment>{$mail->template}</comment> Subject <info>[{$mail->subject}]</info> with ID <comment>{$mail->id}</comment> and date <comment>{$mail->date}</comment>");
                }
                $found++;
                if($update) {
                    $query = Project::query("DELETE FROM mailer_content WHERE mail = :id", [':id' => $mail->id]);
                    if($query = Project::query("DELETE FROM mail WHERE id = :id", [':id' => $mail->id])) {
                        $fixes ++;
                    }
                }
            }
            $query = Project::query("SELECT count(*) as total FROM mail");
            $total = $query->fetchColumn();
            $output->writeln("Found <comment>$found</comment> <info>mail records</info> older thant <comment>$days days</comment> from a total of <comment>$total</comment> records");
            $index += $found;
        }

        if(in_array($scope, ['all', 'blocked'])) {
            $days = (int) $input->getOption('days');
            if($days == -1) $days = 120;
            if($days < 30) {
                throw new \Exception('Number of days must be greater than 120!');
            }

            $output->writeln("Checking old blocked users for mailing...");
            $where = " WHERE action='deny' AND DATE_FORMAT(modified, '%j') > $days";

            $query = Project::query("SELECT * FROM mailer_control $where");

            $found = 0;
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $usr) {
                if($verbose) {
                    $output->writeln("Found blocked subscriber <info>{$usr->email}</info>, Template <comment>{$usr->bounces}</comment> Reason <info>[{$usr->last_reason}]</info> and date <comment>{$usr->modified}</comment>");
                }
                $found++;
                if($update) {
                    if($query = Project::query("DELETE FROM mailer_control WHERE email = :email", [':email' => $usr->email])) {
                        $fixes ++;
                    }
                }
            }
            $query = Project::query("SELECT count(*) as total FROM mailer_control");
            $total = $query->fetchColumn();
            $output->writeln("Found <comment>$found</comment> <info>blocked subscribers</info> older thant <comment>$days days</comment> from a total of <comment>$total</comment> records");
            $index += $found;
        }

        if($index == 0) {
            $output->writeln("<info>No cleaning needed</info>");
        }
        else {
            $output->writeln("<error>Found $index records to clean!</error>");
            if($fixes) {
                $output->writeln("<info>Deleted $fixes records</info>");
            } else {
                $output->writeln("<info>Execute with --update option to delete the old records</info>");
            }
        }


        return;
    }
}
