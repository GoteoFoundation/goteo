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

use Goteo\Model\Call;
use Goteo\Model\Invest;
use Goteo\Model\Matcher;
use Goteo\Model\Node;
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
                      new InputOption('channel', 'n', InputOption::VALUE_OPTIONAL, "If specified, extracts data for the given channel "),
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
        $matcher_id  = $input->getOption('channel');
        $call_id  = $input->getOption('channel');
        $project_id  = $input->getOption('channel');

        $output->writeln("<info>Extract Open Data info</info>");

        if (isset($call_id)) {
            $output->writeln("<info>Retrieving {$call_id}'s data </info>");
            $channel = Call::get($call_id);
        }

        if (isset($channel_id)) {
            $output->writeln("<info>Retrieving {$channel_id}'s data </info>");
            $channel = Node::get($channel_id);
            extractChannelOpenData($channel);
        }

        if (isset($matcher_id)) {
            $output->writeln("<info>Retrieving {$matcher_id}'s data </info>");
            $channel = Matcher::get($matcher_id);
        }

        if (isset($project_id)) {
            $output->writeln("<info>Retrieving {$project_id}'s data </info>");
            $channel = Project::get($project_id);


        }
    }

    private function extractChannelOpenData(Node $channel): void {

        $response = new StreamedResponse(function () use ($channel) {
            $buffer = fopen(time() . '-' . $channel->id , 'w');

            $data = ['id',
                     Text::get('regular-name'),
                     Text::get('regular-email'),
                     'active',
                     'type',
            ];

            fputcsv($buffer, $data);
            fputcsv($buffer, $data);
            fclose($buffer);
        });
    }
}
