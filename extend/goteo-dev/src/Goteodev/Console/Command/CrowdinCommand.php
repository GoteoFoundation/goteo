<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteodev\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Goteo\Console\Command\AbstractCommand;
use Goteo\Application\Config;

class CrowdinCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("dev:crowdin")
             ->setDescription("Initializes the database with some know project status")
              ->setDefinition(array(
                       new InputOption('update', 'u', InputOption::VALUE_NONE, 'Updates the README.md file in Goteo\'s folder'),
                 ))
             ->setHelp(<<<EOT
This script imports data from crowdin (currently top translators members)

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $update  = $input->getOption('update');

        $id = Config::get('crowdin.project');
        $key = Config::get('crowdin.key');
        $skip = Config::get('crowdin.skip_list');
        if(!is_array($skip)) $skip = [];
        if(empty($id) || empty($key)) {
            throw new \Exception('crowdin.project and crowdin.key must be defined in settings.yml');
        }

        $request_url = "https://api.crowdin.com/api/project/$id/reports/top-members/export?format=csv&json&key=$key&language=en";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result);

        $contributors = array_map('str_getcsv', file("https://api.crowdin.com/api/project/$id/reports/top-members/download?key=$key&hash={$json->hash}"));

        $skipped = $valid = [];
        foreach($contributors as $i => $user) {
            if($i === 0) continue; // First line are description
            if(in_array($user[0], $skip)) {
                $skipped[] = $user;
                continue;
            }
            $valid[] = $user;
        }
        if($output->isVerbose()) {
            $output->writeln("Contributors skipped:");
            foreach($skipped as $user) {
                $output->writeln("<comment>{$user[0]}</comment>");
            }
        }

        $output->writeln("Contributors found:");
        foreach($valid as $user) {
            $output->writeln("<info>{$user[0]}</info>");
        }

        if($update) {
            $file = GOTEO_PATH . 'README.md';
            $content = file_get_contents($file);
            $start = strpos($content, '<translators>');
            $end  = strpos($content, '</translators>', $start);

            $part1 = substr($content, 0, $start + 14);
            $part2 = substr($content, $end);


            $new = "$part1\n<ul>\n";
            foreach($valid as $user) {
                $new .=  "\t<li>{$user[0]}</li>\n";
            }
            $new .= "</ul>\n$part2";

            file_put_contents($file, $new);
            $output->writeln("File <info>README.md</info> overwritten");

        } else {
            $output->writeln("Execute with --update to modify the README.md");
        }
     }
}
