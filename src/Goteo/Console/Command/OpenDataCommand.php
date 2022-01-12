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

use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Core\Exception;
use Goteo\Library\FileHandler\File;
use Goteo\Model\Call;
use Goteo\Model\Footprint;
use Goteo\Model\Invest;
use Goteo\Model\Origin;
use Goteo\Model\Project;
use Goteo\Model\Sdg;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class OpenDataCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("opendata")
            ->setDescription("Generates OpenData files")
            ->setDefinition([
                new InputOption('call', 'c', InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given call "),
                new InputOption('sdg', 's', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'If specified, extracts data for the given sdgs'),
                new InputOption('footprint', 'f', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'If specified, extracts data for the given footprints')
            ])
            ->setHelp(<<<EOT
This command generates files using the data from different sources and saves them. The sources can be channels, matchers, calls or projects.

Usage:

Extract Open Data for a call
<info>./console opendata --call goteo </info>

Extract Open Data for a Sdg
<info>./console opendata --sdg 1 </info>

Extract Open Data for multiple Sdgs
<info>./console opendata -s 1 -s 2 -s 3 </info>

Extract Open Data for a Footprint
<info>./console opendata --footprint 1 </info>

Extract Open Data for multiple Footprints
<info>./console opendata -f 1 -f 2 -f 3 </info>


EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->log('Extract OpenData info', [], 'info');

        if ($callOption = $input->getOption('call')) {
            $call_id  = $callOption;
            $this->log("Retrieving {$call_id}'s data", [], 'info');

            try {
                $call = Call::get($call_id);
            } catch (ModelNotFoundException $e) {
                $this->log("Call $call_id does not exist", [], 'error');
                return;
            }

            $this->extractCallOpenData($call);
        }

        if ($listSdg = $input->getOption(('sdg'))) {
            foreach ($listSdg as $sdg_id) {
                $this->log("Retrieving {$sdg_id}'s data", [], 'info');

                $sdg = Sdg::get($sdg_id);
                $this->extractSdgOpenData($sdg);
            }
        }

        if ($listFootprints = $input->getOption('footprint')) {
            foreach ($listFootprints as $footprint_id) {
                $this->log("Retrieving {$footprint_id}'s data", [], 'info');

                $footprint = Footprint::get($footprint_id);
                $this->extractFootprintOpenData($footprint);
            }
        }

    }

    private function extractSdgOpenData(Sdg $sdg): void {

        $this->extractSdgProjects($sdg);
        $this->extractSdgInvests($sdg);
    }

    private function extractSdgProjects(Sdg $sdg) {
        $fileName = time() . '-' . $sdg->id . '-projects.csv';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/sdg/$sdg->id/projects");

        $projects_count = Project::getBySDGs([$sdg->id], 0, 0, true);
        $projects = Project::getBySDGs([$sdg->id], 0, $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    private function extractSdgInvests(Sdg $sdg) {
        $fileName = time() . '-' . $sdg->id . '-invests.csv';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/sdg/$sdg->id/invests");

        $projects_count = Project::getBySDGs([$sdg->id], 0, 0, true);
        $projects = Project::getBySDGs([$sdg->id], 0, $projects_count);

        $invests_count = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);

        $this->extractInvestOpenData($fileName, $invests);

        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    private function extractFootprintOpenData(Footprint $footprint): void {

        $this->extractFootprintProjects($footprint);
        $this->extractFootprintInvests($footprint);
    }

    private function extractFootprintProjects(Footprint $footprint) {
        $fileName = time() . '-' . $footprint->id . '-projects.csv';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/footprint/$footprint->id/projects");

        $projects_count = Project::getByFootprint(['footprints' => $footprint->id], 0, 0, true);
        $projects = Project::getByFootprint(['footprints' => $footprint->id], 0, $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    private function extractFootprintInvests(Footprint $footprint) {
        $fileName = time() . '-' . $footprint->id . '-invests.csv';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/footprint/$footprint->id/invests");

        $projects_count = Project::getBySDGs([$footprint->id], 0, 0, true);
        $projects = Project::getBySDGs([$footprint->id], 0, $projects_count);

        $invests_count = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);

        $this->extractInvestOpenData($fileName, $invests);

        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    private function extractCallOpenData(Call $call): void {

        $this->extractCallProjectsData($call);
        $this->extractCallInvestsData($call);
    }

    private function extractCallProjectsData(Call $call): void {
        $fileName = time() . '-' . $call->id . '-projects.csv';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/call/$call->id/projects");

        $projects_count = Project::getList(['called' => $call->id], 0, 0, true);
        $projects = Project::getList(['called' => $call->id], 0, $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    private function extractCallInvestsData(Call $call): void {
        $fileName = time() . '-' . $call->id . '-invests.csv';
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/call/$call->id/invests");

        $invests_count = Invest::getList(['calls' => $call->id, 'types' => 'nondrop', 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['calls' => $call->id, 'types' => 'nondrop', 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);

        $this->extractInvestOpenData($fileName, $invests);

        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->log("\nUpload of file {$fileName} completed!", [], 'info');
        } else {
            $this->log("\nUpload of file {$fileName} failed!", [], 'error');
        }
    }

    /**
     * @param Project[] $projects
     */
    private function extractProjectOpenData(string $fileName, array $projects): void
    {
        $buffer = fopen('/tmp/' . $fileName, 'w+');
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

        $progress_bar = new ProgressBar($this->output, count($projects));
        $progress_bar->start();

        foreach ($projects as $project) {
            $originVisits = Origin::getList(['project' => $project->id, 'type' => 'referer'], 0, 0, true);
            $projectInvestCount = Invest::getList(['projects' => $project->id, 'types' => 'drop'], null, 0, 0, true);

            fputcsv($buffer, [
                $project->name,
                $project->subtitle,
                $project->description,
                $project->node,
                implode(',', $project->getCategories()),
                implode(',', array_column($project->getSdgs(), 'name')),
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
    }

    /**
     * @param Invest[] $invests
     */
    private function extractInvestOpenData(string $fileName, array $invests): void
    {
        $buffer = fopen('/tmp/' . $fileName, 'w+');
        $data = ['project',
                'amount',
                'date',
                'location',
        ];
        fputcsv($buffer, $data);

        $progress_bar = new ProgressBar($this->output, count($invests));
        $progress_bar->start();

        foreach ($invests as $invest) {
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
    }
}
