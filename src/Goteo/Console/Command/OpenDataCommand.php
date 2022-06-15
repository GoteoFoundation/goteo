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
use Goteo\Core\Model;
use Goteo\Entity\DataSet;
use Goteo\Library\FileHandler\File;
use Goteo\Library\FileHandler\FileInterface;
use Goteo\Model\Call;
use Goteo\Model\Footprint;
use Goteo\Model\Invest;
use Goteo\Model\Node;
use Goteo\Model\Origin;
use Goteo\Model\Project;
use Goteo\Model\Sdg;
use Goteo\Repository\DataSetRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class OpenDataCommand extends AbstractCommand {
    private DataSetRepository $dataSetRepository;

    protected function configure()
    {
        $this->dataSetRepository = new DataSetRepository();

        $this->setName("opendata")
            ->setDescription("Generates OpenData files")
            ->setDefinition([
                new InputOption('call', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given call "),
                new InputOption('sdg', 's', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'If specified, extracts data for the given sdgs'),
                new InputOption('footprint', 'f', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'If specified, extracts data for the given footprints'),
                new InputOption('channel', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'If specified, extracts data for the given channel')
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

        if ($input->getOption('call')) {
            $this->extractDataForCall($input);
        }

        if ($listSdg = $input->getOption(('sdg'))) {
            $this->extractDataForSdg($listSdg);
        }

        if ($listFootprints = $input->getOption('footprint')) {
            $this->extractDataForFootprint($listFootprints);
        }

        if ($listChannels = $input->getOption('channel')) {
            $this->extractDataForChannel($listChannels);
        }
    }

    private function extractSdgOpenData(Sdg $sdg): void
    {
        $this->extractSdgProjects($sdg);
        $this->extractSdgInvests($sdg);
    }

    private function extractSdgProjects(Sdg $sdg) {
        $fileName = $this->getFileName($sdg->id, DataSet::TYPE_PROJECTS);

        $projects_count = Project::getBySDGs([$sdg->id], 0, 0, true);
        $projects = Project::getBySDGs([$sdg->id], 0, $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        $file = $this->getFile('sdg', $sdg->id, DataSet::TYPE_PROJECTS);
        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->logCompleted($fileName);

            try {
                $dataSet = $this->dataSetRepository->getLastBySDGsAndType([$sdg->id], DataSet::TYPE_PROJECTS);
                $this->updateDataSet($dataSet, $file, $fileName, $sdg);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($sdg, $file, $fileName, DataSet::TYPE_PROJECTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractSdgInvests(Sdg $sdg) {
        $fileName = $this->getFileName($sdg->id, DataSet::TYPE_INVESTS);

        $projects_count = Project::getBySDGs([$sdg->id], 0, 0, true);
        $projects = Project::getBySDGs([$sdg->id], 0, $projects_count);

        $invests_count = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);
        $this->extractInvestOpenData($fileName, $invests);

        $file = $this->getFile('sdg', $sdg->id, DataSet::TYPE_INVESTS);
        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->logCompleted($fileName);

            try {
                $dataSet = $this->dataSetRepository->getLastBySDGsAndType([$sdg->id], DataSet::TYPE_INVESTS);
                $this->updateDataSet($dataSet, $file, $fileName, $sdg);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($sdg, $file, $fileName, DataSet::TYPE_INVESTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractFootprintOpenData(Footprint $footprint): void
    {
        $this->extractFootprintProjects($footprint);
        $this->extractFootprintInvests($footprint);
    }

    private function extractFootprintProjects(Footprint $footprint) {
        $fileName = $this->getFileName($footprint->id, DataSet::TYPE_PROJECTS);

        $projects_count = Project::getByFootprint(['footprints' => $footprint->id], 0, 0, true);
        $projects = Project::getByFootprint(['footprints' => $footprint->id], 0, $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        $file = $this->getFile('footprint', $footprint->id, DataSet::TYPE_PROJECTS);
        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->logCompleted($fileName);
            try {
                $dataSet = $this->dataSetRepository->getLastByFootprintAndType([$footprint->id], DataSet::TYPE_PROJECTS);
                $this->updateDataSet($dataSet, $file, $fileName, $footprint);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($footprint, $file, $fileName, DataSet::TYPE_PROJECTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractFootprintInvests(Footprint $footprint) {
        $fileName = $this->getFileName($footprint->id, DataSet::TYPE_INVESTS);

        $projects_count = Project::getBySDGs([$footprint->id], 0, 0, true);
        $projects = Project::getBySDGs([$footprint->id], 0, $projects_count);

        $invests_count = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['projects' => $projects, 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);
        $this->extractInvestOpenData($fileName, $invests);

        $file = $this->getFile('footprint', $footprint->id, DataSet::TYPE_INVESTS);
        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->logCompleted($fileName);

            try {
                $dataSet = $this->dataSetRepository->getLastByFootprintAndType([$footprint->id], DataSet::TYPE_INVESTS);
                $this->updateDataSet($dataSet, $file, $fileName, $footprint);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($footprint, $file, $fileName, DataSet::TYPE_INVESTS);
            }

        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractCallOpenData(Call $call): void
    {
        $this->extractCallProjectsData($call);
        $this->extractCallInvestsData($call);
    }

    private function extractCallProjectsData(Call $call): void {
        $fileName = $this->getFileName($call->id, DataSet::TYPE_PROJECTS);

        $projects_count = Project::getList(['called' => $call->id], null, 0, 0, true);
        $projects = Project::getList(['called' => $call->id], null, 0,  $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        $file = $this->getFile('call', $call->id, DataSet::TYPE_PROJECTS);
        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->logCompleted($fileName);
            try {
                $dataSet = $this->dataSetRepository->getLastByCAllAndType([$call->id], DataSet::TYPE_PROJECTS);
                $this->updateDataSet($dataSet, $file, $fileName, $call);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($call, $file, $fileName, DataSet::TYPE_PROJECTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractCallInvestsData(Call $call): void {
        $fileName = $this->getFileName($call->id, DataSet::TYPE_INVESTS);

        $invests_count = Invest::getList(['calls' => $call->id, 'types' => 'nondrop', 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['calls' => $call->id, 'types' => 'nondrop', 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);
        $this->extractInvestOpenData($fileName, $invests);

        $file = $this->getFile('call', $call->id, DataSet::TYPE_INVESTS);
        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->logCompleted($fileName);
            try {
                $dataSet = $this->dataSetRepository->getLastByCallAndType([$call->id], DataSet::TYPE_INVESTS);
                $this->updateDataSet($dataSet, $file, $fileName, $call);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($call, $file, $fileName, DataSet::TYPE_INVESTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractChannelOpenData(Node $channel): void
    {
        $this->extractChannelProjects($channel);
        $this->extractChannelInvests($channel);
    }

    private function extractChannelProjects(Node $channel): void
    {
        $fileName = $this->getFileName($channel->id, DataSet::TYPE_PROJECTS);

        $projects_count = Project::getList(['node' => $channel->id], null, 0, 0, true);
        $projects = Project::getList(['node' => $channel->id], null, 0, $projects_count);
        $this->extractProjectOpenData($fileName, $projects);

        $file = $this->getFile('channel', $channel->id, DataSet::TYPE_PROJECTS);
        if ($file->upload('/tmp/' . $fileName, $fileName)) {
            $this->logCompleted($fileName);
            try {
                $dataSet = $this->dataSetRepository->getLastByChannelAndType([$channel->id], DataSet::TYPE_PROJECTS);
                $this->updateDataSet($dataSet, $file, $fileName, $channel);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($channel, $file, $fileName, DataSet::TYPE_PROJECTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
    }

    private function extractChannelInvests(Node $channel): void
    {
        $fileName = $this->getFileName($channel->id, DataSet::TYPE_INVESTS);

        $invests_count = Invest::getList(['node' => $channel->id, 'types' => 'nondrop', 'status' => Invest::STATUS_CHARGED], null, 0, 0, true);
        $invests = Invest::getList(['node' => $channel->id, 'types' => 'nondrop', 'status' => Invest::STATUS_CHARGED], null, 0, $invests_count);
        $this->extractInvestOpenData($fileName, $invests);

        $file = $this->getFile('channel', $channel->id, DataSet::TYPE_INVESTS);
        if ( $file->upload('/tmp/' . $fileName, $fileName) ) {
            $this->logCompleted($fileName);
            try {
                $dataSet = $this->dataSetRepository->getLastByChannelAndType([$channel->id], DataSet::TYPE_INVESTS);
                $this->updateDataSet($dataSet, $file, $fileName, $channel);
            } catch (ModelNotFoundException $e) {
                $this->createDataSet($channel, $file, $fileName, DataSet::TYPE_INVESTS);
            }
        } else {
            $this->logError($fileName);
        }
        $file->close();
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

    protected function extractDataForCall(InputInterface $input): void
    {
        $callOption = $input->getOption('call');
        $calls = [];

        if (!current($callOption)) {
            $calls = Call::getList(['available' => true]);
        } else {
            foreach ($callOption as $call) {
                try {
                    $calls[] = Call::get($call);
                } catch (Exception $e) {
                    $this->log("Call $call does not exist", [], 'error');
                }
            }
        }

        foreach ($calls as $call) {
            $this->extractCallOpenData($call);
        }
    }

    protected function extractDataForSdg(array $listSdg): void
    {
        if (!current($listSdg)) {
            $countSdg = Sdg::getList([], 0, 0, true);
            $listSdg = array_column(Sdg::getList([], 0, $countSdg), 'id');
        }

        foreach ($listSdg as $sdg_id) {
            $this->log("Retrieving {$sdg_id}'s data", [], 'info');

            $sdg = Sdg::get($sdg_id);
            $this->extractSdgOpenData($sdg);
        }
    }

    protected function extractDataForFootprint(array $listFootprints): void
    {
        if (!current($listFootprints))
            $listFootprints = array_column(Footprint::getList(), 'id');

        foreach ($listFootprints as $footprint_id) {
            $this->log("Retrieving {$footprint_id}'s data", [], 'info');

            $footprint = Footprint::get($footprint_id);
            $this->extractFootprintOpenData($footprint);
        }
    }

    protected function extractDataForChannel(array $listChannels): void
    {
        if (!current($listChannels))
            $listChannels = array_column(Node::getList(), 'id');

        foreach ($listChannels as $channel_id) {
            $this->log("Retrieving {$channel_id}'s data", [], 'info');

            $channel = Node::get($channel_id);
            $this->extractChannelOpenData($channel);
        }
    }

    private function getFileName($id, string $type): string
    {
        return time() . "-$id-$type.csv";
    }

    private function getFile(string $model, $id, string $type): FileInterface
    {
        $file = File::factory(['bucket' => AWS_S3_BUCKET_DOCUMENT]);
        $file->connect();
        $file->setPath("open_data/$model/$id/$type");
        return $file;
    }

    private function createDataSet(Model $model, FileInterface $file, string $fileName, string $type): void
    {
        $dataSet = new DataSet();
        $dataSet->setTitle($model->name);
        $dataSet->setUrl($file->get_public_url($file->get_path() . $fileName));
        $dataSet->setType($type);
        $this->dataSetRepository->persist($dataSet);
        $model->addDataSet($dataSet, 1);
    }

    private function updateDataSet(DataSet $dataSet, FileInterface $file, string $fileName, Model $model): void
    {
        $dataSet->setUrl($file->get_public_url($file->get_path() . $fileName));
        $this->dataSetRepository->persist($dataSet);
        $model->addDataSet($dataSet, 1);
    }

    private function logError(string $fileName): void
    {
        $this->log("\nUpload of file $fileName failed!", [], 'error');
    }

    private function logCompleted(string $fileName): void
    {
        $this->log("\nUpload of file {$fileName} completed!", [], 'info');
    }
}
