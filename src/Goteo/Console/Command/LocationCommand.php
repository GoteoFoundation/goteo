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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Finder\Finder;

use Goteo\Application\Config;
use Goteo\Model\Project;
use Goteo\Model\Project\ProjectLocation;
use Goteo\Model\User\UserLocation;
use Goteo\Util\Google\GoogleGeocoder;

class LocationCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("location")
             ->setDescription("Manages location issues")
             ->setDefinition(array(
                new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the repair action, read-only operation otherwise'),
                new InputArgument('table', InputArgument::REQUIRED, 'Table to check locations: [projects]'),
                new InputOption('project', 'p', InputOption::VALUE_OPTIONAL, 'Only processes the specified Project ID'),
                new InputOption('call', 'c', InputOption::VALUE_OPTIONAL, 'Only processes projects in the specified Call ID'),
                new InputOption('status', 's', InputOption::VALUE_OPTIONAL, 'Checks only projects with status [valid|all|accepted|call]','valid'),
                new InputOption('method', 'm', InputOption::VALUE_OPTIONAL, 'Uses only the method specified [owner|google], otherwise the owner location will be used and google if that does\'nt exists', 'all'),

                ))
             ->setHelp(<<<EOT
This command tries to add location entries to unlocated items (Projects or others)

Usage:

Check how many projects can be added the owner's location
<info>./console location projects</info>

Fix projects by adding the owner's location
<info>./console location projects -u</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = $input->getArgument('table');
        $update = $input->getOption('update');
        $project = $input->getOption('project');
        $call = $input->getOption('call');
        $status = $input->getOption('status');
        $method = $input->getOption('method');

        if(!in_array($status, ['all','valid','accepted','call'])) {
            throw new \Exception('Status is not valid!');
        }
        if(!in_array($method, ['all','owner','google'])) {
            throw new \Exception('Method is not valid!');
        }

        $fixable = 0;
        $gfixable = 0;
        $fixes = 0;
        switch ($table) {
            case 'projects':
                $filter = [];
                if($status === 'all') {
                    $filter['status'] = -3;
                }
                if($status === 'accepted') {
                    $filter['status'] = -2;
                }
                if($status === 'call') {
                    $filter['status'] = -3;
                    $filter['called'] = 'all';
                }
                if($project) {
                    $filter['proj_id'] = $project;
                }
                elseif($call) {
                    $filter['called'] = $call;
                    $filter['located'] = 'unlocated';
                }
                else {
                    $filter['located'] = 'unlocated';
                }
                $total_unlocated = Project::getList($filter, null, 0, 0, true);
                $output->writeln("<info>Total unlocated projects: </info><error>$total_unlocated</error>");
                $unlocated = Project::getList($filter, null, $offset, $total_unlocated);
                foreach($unlocated as $proj) {
                    $plocation = ProjectLocation::get($proj);
                    $p_loc = null;
                    $_method = '<info>OWNER</info>';
                    if(($ulocation = UserLocation::get($proj->owner)) && in_array($method, ['owner', 'all'])) {
                        if($ulocation->validate()) {
                            $p_loc = [
                                'method' => $ulocation->method,
                                'locable' => $ulocation->locable,
                                'city' => $ulocation->city,
                                'region' => $ulocation->region,
                                'country' => $ulocation->country,
                                'country_code' => $ulocation->country_code,
                                'longitude' => $ulocation->longitude,
                                'latitude' => $ulocation->latitude,
                                'radius' => $ulocation->radius,
                                'info' => $ulocation->info,
                                'id' => $proj->id
                            ];
                            $fixable ++;
                        }
                    }
                    if(!$p_loc && in_array($method, ['google', 'all'])) {
                        $_method = '<fg=red>GOOGLE</fg=red>';
                        // check if can be reverse geocoded
                        if($proj->location) {
                            $gfixable ++;
                            $p_loc = [
                                'method' => 'manual',
                                'locable' => true,
                                'radius' => 0,
                                'info' => '',
                                'id' => $proj->id
                            ];
                            // Check Google
                            if($update || $output->isVerbose()) {
                                if($data = GoogleGeocoder::getCoordinates(array('address' => $proj->location))) {
                                    $p_loc['latitude'] = $data['latitude'];
                                    $p_loc['longitude'] = $data['longitude'];
                                    $p_loc['city'] = $data['city'];
                                    $p_loc['region'] = $data['region'];
                                    $p_loc['country'] = $data['country'];
                                    $p_loc['country_code'] = $data['country_code'];
                                    // echo "GEOLOCATED: lat,lng: [{$data['latitude']},{$data['longitude']}] city: [{$data['city']}] country: [{$data['country_code']}, {$data['country']}] region [{$data['region']}]";
                                } else {
                                    $p_loc = null;
                                    $gfixable --;
                                }
                            }
                        }
                    }
                    if($p_loc) {
                        if($update || $output->isVerbose()) {
                            $output->write($proj->id . ' BY ' . $proj->owner .
                                ($plocation ? " <error>ALREADY LOCATED in {$plocation->city} ({$plocation->region}) {$plocation->country_code}</error>" : '') .
                                " CAN BE LOCATED IN <comment>{$p_loc[city]} ({$p_loc[region]}) {$p_loc[country_code]}</comment> USING $_method");
                            if($update) {
                                $new_loc = new ProjectLocation($p_loc);
                                $errors = [];
                                if($new_loc->save($errors)) {
                                    $output->write(' <info>LOCATION UPDATED</info>');
                                    $fixes ++;
                                } else {
                                    $output->write(' <error>ERROR: ' . implode(', ', $errors) . '</error>');
                                }
                            }
                            $output->writeln('');
                        }
                    } else  {
                        if($output->isVerbose()) {
                            $output->writeln("<error>Cannot find location string or owner location for project [{$proj->id}]</error>");
                        }
                    }

                }
                $output->writeln("<error>$fixable</error> <info>projects can be fixed by adding owners location</info>");
                $output->writeln("<error>$gfixable</error> <info>projects can be fixed by querying Google geolocator</info>");
                break;

            default:
                $output->writeln("<error>Not found table [$table]</error>");
                break;
        }
        if($fixable == 0) {
            $output->writeln("<info>No problems found</info>");
        }
        else {
            if($fixes) {
                $output->writeln("<info>Repaired $fixes items</info>");
            } else {
                $output->writeln("<info>Execute with --update option to fix the problems</info>");
            }
        }
        return;

    }
}
