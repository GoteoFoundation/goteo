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
use Symfony\Component\Process\PhpExecutableFinder;
use Cron\Cron;
use Cron\Job\JobInterface;
use Cron\Job\ShellJob;
use Cron\Schedule\CrontabSchedule;
use Cron\Executor\Executor;
use Cron\Resolver\ResolverInterface;
use Cron\Resolver\ArrayResolver;
use Symfony\Component\Yaml\Yaml;

use Goteo\Application\Config;

class CronCommand extends AbstractCommand {
    protected static $resolver = null;
    protected static $crontabLines = [];

    public static function setResolver(ResolverInterface $resolver) {
        static::$resolver = $resolver;
    }

    public static function getResolver() {
        // Initialize resolver if null
        if(!static::$resolver instanceOf ResolverInterface) {
            static::setResolver(new ArrayResolver());
        }
        return static::$resolver;
    }

    /**
     * Adds a job
     * @param JobInterface $job [description]
     */
    public static function addJob(JobInterface $job) {
        return static::getResolver()->addJob($job);
    }

    /**
     * Automatically adds a scheduled job from a shell command passed
     * @param [type] $command  the shell executable program ()
     * @param [type] $schedule the crontab time line (ex: 5 * * * *)
     */
    public static function addSchedule($command, $schedule) {
        chdir(GOTEO_PATH);
        $job = new ShellJob();
        $job->setCommand($command);
        $job->setSchedule(new CrontabSchedule($schedule));
        return static::addJob($job);
    }

    /**
     * Adds a line of crontab
     * Format:
     * array (
     *     'schedule' => 15 * * * *
     *     'command' => 'command_to_execute'
     *     'type' => php|shell
     *     'nice' => true|false
     * )
     * @param array $job [description]
     */
    public static function addCrontabLine(array $job) {
        $executable = $job['command'];
        if($job['type'] == 'php') {
            $executable = (new PhpExecutableFinder())->find() . " $executable";
        }
        if($job['nice']) {
            $executable = "nice $executable";
        }
        static::$crontabLines[] = [$executable, $job['schedule']];
        static::addSchedule($executable, $job['schedule']);
    }

    /**
     * On construct, let's find yaml predefined tasks
     * @param [type] $name [description]
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        // Change to Goteo dir
        chdir(GOTEO_PATH);
        // Initializes crontab from yml resource file
        if(is_file(GOTEO_PATH . 'Resources/crontab.yml')) {
            $crontab = Yaml::parse(file_get_contents(GOTEO_PATH . 'Resources/crontab.yml'));
            $env = Config::get('env');
            if(isset($crontab[$env])) {
                foreach($crontab[$env] as $job) {
                    static::addCrontabLine($job);
                }
            }
        }
    }

    protected function configure()
    {
        $this->setName("cron")
             ->setDescription("The cron program executor")
             ->setDefinition(array(
                    new InputOption('crontab', 'c', InputOption::VALUE_NONE, 'List the crontab defined and exit'),
                    new InputOption('jobs', 'j', InputOption::VALUE_NONE, 'List the jobs that will be executed now and exit'),
                ))
             ->setHelp(<<<EOT
Centralizes the execution of scheduled tasks.

Put in your crontab:
<info>
* * * * * /path/to/php /path/to/goteo/bin/console cron --lock >/dev/null 2>&1
</info>
In php plugins, Tasks can be added such as:
<fg=cyan>
<?php

\$job = new \Cron\Job\ShellJob();
\$job->setCommand('ls /path/to/folder/');
\$job->setSchedule(new \Cron\Schedule\CrontabSchedule('0 0 * * *'));
CronCommand::addJob(\$job)

// Or simply:
CronCommand::addSchedule('ls /path/to/folder/', '0 0 * * *');

?>
</>
Cron syntax:
<fg=cyan>
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)
</>
EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if(count(static::$crontabLines) == 0) {
            $output->writeln("<fg=red>No crontab at all!</>");
            return;
        }
        if($input->getOption('crontab')) {
            foreach(static::$crontabLines as $line) {
                $output->writeln($line[1] . ' ' . $line[0]);
            }
            return;
        }

        if($input->getOption('jobs')) {
            $jobs = static::getResolver()->resolve();
            foreach($jobs as $i => $job) {
                $schedule = $job->getSchedule()->getPattern();
                $task = $job->getProcess()->getCommandLine();
                $output->writeln("$schedule $task");
            }
            if($i == 0) {
                $output->writeln("<fg=cyan>No jobs now</>");
            }
            return;
        }

        $cron = new Cron();
        $cron->setExecutor(new Executor());
        $cron->setResolver(static::getResolver());
        $cron_report = $cron->run();
        $num = count($cron_report->getReports());
        $this->info("Running $num processes", ['processes' => $num]);
        // wait
        while($cron->isRunning()) {
            $output->write(".");
            usleep(100000);
        }
        $output->writeln("\n");

        $i = 0;
        foreach($cron_report->getReports() as $report) {
            $process = $report->getJob()->getProcess();
            $task = $report->getJob()->getProcess()->getCommandLine();
            $time = $report->getEndTime() - $report->getStartTime();
            $schedule = $report->getJob()->getSchedule()->getPattern();
            if($output->isVerbose()) {
                $output->writeln('[<fg=cyan>'. $task . '</>] <option=bold;fg=' . ($report->isSuccessful() ? 'green>OK' : 'red>ERROR') . '</>');
            }
            if($report->isSuccessful()) {
                $this->info("Completed [$task] " . round($time, 3) . " seconds", ['time' => $time, 'job' => $task, 'schedule' => $schedule]);
                if($output->isVerbose()) {
                    $output->writeln('<fg=blue>'. implode("\n", $report->getOutput()) . '</>');
                }

            } else {
                $errors = [];
                foreach($report->getError() as $err) {
                    $errors = array_merge($errors, explode("\n", $err));
                }
                $this->error("Failed [$task] " . round($time, 3) . " seconds", ['time' => $time, 'job' => $task, 'schedule' => $schedule, 'error' => $errors]);
                if($output->isVerbose()) {
                    $output->writeln('<fg=red>'. implode("\n", $report->getError()) . '</>');
                }
            }
            $i++;
        }
        if($i == 0) {
            $output->writeln("<fg=cyan>No jobs now</>");
        }
        elseif($cron_report->isSuccessful()) {
            $output->writeln("<info>All done</info>");
        }
        else {
            $output->writeln("<error>Completed with errors</error>");
        }
    }
}
