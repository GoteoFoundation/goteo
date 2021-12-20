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

use Goteo\Library\FileHandler\File;
use Goteo\Model\Call;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\Node;
use Goteo\Model\Origin;
use Goteo\Model\Project;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OpenDataCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("opendata")
             ->setDescription("Generates OpenData files")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                      new InputOption('call', 'c', InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given call "),
                      new InputOption('channel', '', InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given channel "),
                      new InputOption('matcher', 'm', InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given matcher "),
                      new InputOption('project', 'p', InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given project "),
                ))
             ->setHelp(<<<EOT
This command generates files using the data from different sources and saves them. The sources can be channels, matchers, calls or projects.

Usage:

Extract Open Data for a channel
<info>./console opendata --channel goteo --update </info>

Update the provided channel's summary data
<info>./console opendata --channel channel_id --update </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');
        $channel_id  = $input->getOption('channel');
        $matcher_id  = $input->getOption('matcher');
        $call_id  = $input->getOption('call');
        $project_id  = $input->getOption('project');

        $this->log('Extract OpenData info', [], 'info');

        if (isset($call_id)) {
            $this->log("Retrieving {$call_id}'s data", [], 'info');

            $call = Call::get($call_id);
            $this->extractCallOpenData($call);
        }

        if (isset($channel_id)) {
            $this->log("Retrieving {$channel_id}'s data", [], 'info');
            $channel = Node::get($channel_id);
        }

        if (isset($matcher_id)) {
            $this->log("Retrieving {$matcher_id}'s data", [], 'info');
            $matcher = Matcher::get($matcher_id);
        }

        if (isset($project_id)) {
            $this->log("Retrieving {$project_id}'s data", [], 'info');
            $project = Project::get($project_id);
        }
    }

    private function extractCallOpenData(Call $call): void {

        $this->extractProjectsData($call);
        $this->extractInvestsData($call);
    }

    private function extractProjectsData(Call $call): void {
        $fileName = time() . '-' . $call->id . '-projects';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath('open_data');
        $buffer = fopen('/tmp/' . $fileName, 'wr');

        $data = ['name',
                'subtitle',
                'description',
                'nodes',
                'category',
                'sdgs',
                'social_commitment',
                'date_init',
                'date_end',
                'campaing_end',
                'location',
                'minimum_amount',
                'optimal_amount',
                'amount',
                'visits',
                'donors',
                'matched_donors'
        ];

        fputcsv($buffer, $data);

        $projects_count = Project::getList(['calls' => $call->id], 0, 0, true);
        $projects = Project::getList(['calls' => $call->id, 0, $countProjects]);
        $progress_bar = new ProgressBar($this->output, $projects_count);
        $progress_bar->start();

        foreach($projects as $project) {
            $originVisits = Origin::getList(['project' => $project->id, 'type' => 'referer'], 0, 0, true);
            $projectInvestCount = Invest::getList(['projects' => $project->id, 'types' => 'drop'], null, 0, 0, true);

            fputcsv($buffer, [
                $project->name,
                $project->subtitle,
                $project->description,
                $project->node,
                implode(',',$project->getCategories()),
                implode(',', array_column($project->getSdgs(), null, 'name')),
                $project->getSocialCommitment()->name,
                $project->published,
                $project->passed,
                $project->closed,
                $project->location,
                $project->mincost,
                $project->maxcost,
                $project->amount,
                $originVisits,
                $project->num_investors,
                $projectInvestCount
            ]);

            $progress_bar->advance();
        }

        $progress_bar->finish();
        fclose($buffer);
        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    private function extractInvestsData(Call $call): void {
        $fileName = time() . '-' . $call->id . '-invests';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);

        $file->connect();
        $file->setPath('open_data');

        $buffer = fopen('/tmp/' . $fileName , 'wr');
        $data = ['project',
                'amount',
                'date',
                'location',
        ];
        fputcsv($buffer, $data);

        $invests_count = Invest::getList(['calls' => $call->id, 'types' => 'nondrop'], null, 0, 0, true);
        $invests = Invest::getList(['calls' => $call->id, 'types' => 'nondrop'], null, 0, $callInvestCount);


        $progress_bar = new ProgressBar($this->output, $invests_count);
        $progress_bar->start();

        foreach($invests as $invest) {
            fputcsv($buffer, [
                $invest->project,
                $invest->amount,
                $invest->charged,
                $invest->getLocation()->city
            ]);

            $progress_bar->advance();
        }
        $progress_bar->finish();
        fclose($buffer);

        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }
}
