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
    private $id;
    private $key;

    protected function configure()
    {
        $this->setName("dev:crowdin")
             ->setDescription("Initializes the database with some know project status")
              ->setDefinition(array(
                       new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. Updates the README.md or uploads data'),
                       new InputArgument('scope', InputArgument::REQUIRED, 'Operation scope: [readme]'),
                 ))
             ->setHelp(<<<EOT
This script imports data from crowdin

Usage:

Shows the list of translators from translate.goteo.org
<info>./console dev:crowdin readme</info>

Updates the README.md with the list of translators from translate.goteo.org
<info>./console dev:crowdin readme -u</info>
EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $update  = $input->getOption('update');
        $scope  = $input->getArgument('scope');

        $this->id = Config::get('crowdin.project');
        $this->key = Config::get('crowdin.key');
        if(!is_array($skip)) $skip = [];
        if(empty($this->id) || empty($this->key)) {
            throw new \Exception('crowdin.project and crowdin.key must be defined in settings.yml');
        }

        if($scope === 'readme') {
            $this->readme($input, $output);
        }

    }

    protected function readme(InputInterface $input, OutputInterface $output) {
        $update  = $input->getOption('update');

        $skip = Config::get('crowdin.skip_list');
        $id = $this->id;
        $key = $this->key;
        $request_url = "https://api.crowdin.com/api/project/$id/reports/top-members/export?format=csv&json&key=$key&language=en";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($result);

        $contributors = array_map('str_getcsv', file("https://api.crowdin.com/api/project/$id/reports/top-members/download?key=$key&hash={$json->hash}"));

        $skipped = $valid = $titles = $info = [];
        foreach($contributors as $i => $user) {
            if($i === 0) {
                $titles = $user;
                continue; // First line are description
            }
            foreach($user as $i => $u) {
                if($i === 0) continue;
                if($u) $info[$user[0]] .= " <comment>{$titles[$i]}</comment>: <fg=cyan>{$u}</>";
            }

            if(in_array($user[0], $skip)) {
                $skipped[] = $user;
                continue;
            }
            $valid[] = $user;
        }


        if($output->isVerbose()) {
            $output->writeln("\nContributors skipped:");
            foreach($skipped as $user) {
                $output->writeln("<info>{$user[0]}</info>" . $info[$user[0]]);
            }
        }

        $output->writeln("\nContributors found:");
        foreach($valid as $user) {
            $output->writeln("<info>{$user[0]}</info>" . $info[$user[0]]);
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
