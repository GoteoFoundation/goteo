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
use Symfony\Component\Console\Output\BufferedOutput;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Goteo\Util\AnsiConverter\Theme\SolarizedLightTheme;

use Goteo\Application\Config;
use Goteo\Model\Project;
use Goteo\Model\Template;
use Goteo\Model\Mail;

/**
 *  Several database checks
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
                      new InputOption('days', 'd', InputOption::VALUE_REQUIRED, 'Number of days that a record is considered "old" (120 by default, 365 for mails)', -1),
                      new InputArgument('scope', InputArgument::OPTIONAL, 'Optional operation scope (default all): [all|feed|token|mailing|blocked|toolkit]', 'all')
                ))

             ->setHelp(<<<EOT
This script cleans old records from several tables:
- mail table
- feed table
Also removes old generated tokens (by password change for example)

Usage:

User the --update option to actually make changes to the database
<info>./console dbverify all|feed|mailing|token|blocked|toolkit [--update]</info>

Examples:

Delete feed entries older than 60 days
<info>./console dbverify feed -d 60 -u</info>

Delete mail entries older than 120 days
<info>./console dbverify mailing -d 120 -u</info>

Delete email change tokens older than 4 days
<info>./console dbverify token -u</info>

Delete blocked emails (due complains from AmazonSES) older than 40 days
<info>./console dbverify blocked -d 40 -u</info>

Use the <info>toolkit</info> command to check several potencial issues in the database
<info>./console dbverify toolkit </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');
        $scope = $input->getArgument('scope');
        if(!in_array($scope, ['all', 'feed', 'mailing', 'token', 'blocked', 'toolkit', 'predict'])) {
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
            if($days == -1) $days = 365;
            if($days < 30) {
                throw new \Exception('Number of days must be greater than 30!');
            }

            $output->writeln("Checking old mail data...");
            $where = " WHERE (template NOT IN (:template1, :template2) OR template IS NULL)
                        AND DATE_FORMAT(from_unixtime(unix_timestamp(now()) - unix_timestamp(`date`)), '%j') > $days";

            $query = Project::query("SELECT * FROM mail $where", [':template1' => Template::NEWSLETTER, ':template2' => Template::MESSAGE_DONORS]);

            $found = 0;
            foreach ($query->fetchAll(\PDO::FETCH_CLASS) as $mail) {
                if($verbose) {
                    $output->writeln("Found old mail <info>{$mail->email}</info>, Template <comment>{$mail->template}</comment> Subject <info>[{$mail->subject}]</info> with ID <comment>{$mail->id}</comment> and date <comment>{$mail->date}</comment>");
                }
                $found++;
                if($update) {
                    $query = Project::query("DELETE FROM mailer_content WHERE mail = :id", [':id' => $mail->id]);
                    if($query = Project::query("DELETE FROM mail WHERE id = :id", [':id' => $mail->id])) {
                        Project::query("DELETE FROM mail_stats WHERE mail_id = :id", [':id' => $mail->id]);
                        $fixes ++;
                    }
                }
            }
            if($update) {
                // Project::query("DELETE FROM mail_stats_location WHERE id NOT IN (SELECT id FROM mail_stats)");
                // Project::query("OPTIMIZE TABLE mail");
                // Project::query("OPTIMIZE TABLE mail_content");
                // Project::query("OPTIMIZE TABLE mail_stats");
                // Project::query("OPTIMIZE TABLE mail_stats_location");
            }
            $query = Project::query("SELECT count(*) as total FROM mail");
            $total = $query->fetchColumn();
            $output->writeln("Found <comment>$found</comment> <info>mail records</info> older than <comment>$days days</comment> from a total of <comment>$total</comment> records");
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

        if(in_array($scope, ['all', 'toolkit'])) {
            $html = "<h2>Commands executed</h2>";

            $converter = new AnsiToHtmlConverter(new SolarizedLightTheme());
            $nerrors = 0;

            $scopes = ['poolamount' => "Checking pool amounts in users...",
                       'project' => "Checking projects calculated amounts...",
                       'comments' => "Checking number of comments in posts..."];
            $command = $this->getApplication()->find('toolkit');
            // $out = new BufferedOutput($output->getVerbosity(), $output->isDecorated());
            $out = new BufferedOutput(BufferedOutput::VERBOSITY_VERBOSE, true);
            foreach($scopes as $key => $text) {
                $output->writeln($text);
                $output->writeln('<comment>'.$_SERVER['argv'][0] ." toolkit $key</comment>");
                $args = new ArrayInput(['command' => 'toolkit', 'scope' => $key]);
                if($command->run($args, $out) !== 0) {
                    $nerrors++;
                }
                $html .= "<h3>bin/console toolkit $key</h3>\n";
                $res = $out->fetch();
                $html .= '<p>'.nl2br($converter->convert($res)).'</p>';
                if($output->isVerbose()) {
                    $output->writeln($res);
                }
            }

            if($nerrors) {
                $output->writeln("<error>Errors found!</error>");
                $index = $nerrors;
                $mailer = Mail::createFromHtml(Config::getMail('fail'), '', "DATABASE INCONSISTENCY in [" .Config::get('url.main')."]", $html);
                $errors = [];
                if(!$mailer->send($errors)) {
                    throw new \RuntimeException('Error sending email: ' . implode("\n", $errors));
                }
            } else {
                $output->writeln("<info>Everything ok</info>");
            }
        }

        if(in_array($scope, ['all', 'predict'])) {
            $html = "<h2>PROJECT FAILURE PREDICTIONS:</h2>";
            $converter = new AnsiToHtmlConverter(new SolarizedLightTheme());

            $command = $this->getApplication()->find('endround');
            $out = new BufferedOutput(BufferedOutput::VERBOSITY_NORMAL, true);
            $args = new ArrayInput(['command' => 'endround', '--skip-invests' => true, '--predict' => 1]);
            // $command->setOutput($out);
            // $command->setInput($args);
            // print_r($command->output);die;
            if($command->run($args, $out) !== 0) {
                $output->writeln("<error>Errors found!</error>");
                $res = $out->fetch();
                $html .= '<p>' . nl2br($converter->convert($res)) .'</p>';
                if($output->isVerbose()) {
                    $output->writeln($res);
                }
                $index = 1;
                $mailer = Mail::createFromHtml(Config::getMail('fail'), '', "FAILING PROJECTS PREDICTION in [" .Config::get('url.main')."]", $html);
                $errors = [];
                if(!$mailer->send($errors)) {
                    throw new \RuntimeException('Error sending email: ' . implode("\n", $errors));
                }
            } else {
                $output->writeln("<info>Everything ok</info>");
            }
        }

        if($index == 0) {
            $output->writeln("<info>No cleaning needed</info>");
        }
        else {
            $output->writeln("<error>Found $index issues!</error>");
            if($fixes) {
                $output->writeln("<info>Deleted $fixes records</info>");
            } else {
                $output->writeln("<info>Execute with --update option to delete the old records</info>");
            }
        }


        return;
    }
}
