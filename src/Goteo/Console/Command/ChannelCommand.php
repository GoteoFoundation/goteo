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

use Goteo\Model\Node;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ChannelCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("channel")
             ->setDescription("Manages channels")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                      new InputOption('channel', 'c', InputOption::VALUE_OPTIONAL, "If specified, updates node_id's node_data ")
                ))
             ->setHelp(<<<EOT
This command updates the channels summary data.

Usage:

Update channels summary data
<info>./console channel --update </info>

Update the provided channel's summary data
<info>./console channel --chanel channel_id --update </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');
        $channel_id  = $input->getOption('channel');
        $channel_provided = isset($channel_id);

        $output->writeln("<info>Update channels thrown</info>");

        if ($channel_provided) {
            $output->writeln("<info>Update {$channel_id}'s data </info>");
            $channel = Node::get($channel_id);
            if ($update) {
                $channel->updateData();
            }
        } else {
            $channels = Node::getList();
            $count = count($channels);
            $output->writeln("<info>About to update {$count} channels </info>");

            foreach(Node::getAll() as $channel) {
                $output->writeln("<info>Update {$channel->name}'s data </info>");
                if ($update) {
                    if (!$channel->updateData()) {
                        $this->warning("<error>Error updating {$channel->name->name}'s data</error>");
                        $output->writeln("<error> Error updating {$channel->name->name}'s data </error>");
                    }
                }
            }
        }
    }
}
