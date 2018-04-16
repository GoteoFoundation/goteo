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
use Goteo\Application\Lang;
use Goteo\Library\Text;
use FileSystemCache;


class CacheCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("cache")
             ->setDescription("Manages cache files")
             ->setDefinition(array(
                      new InputOption('clear', 'c', InputOption::VALUE_REQUIRED, 'Clears files. specify one of [config|sql|images]'),
                ))
             ->setHelp(<<<EOT
This command may be used to clear cache files (generated from SQL, yaml, files, etc)

Usage:

Clean config cached files (yaml files mostly, settings and localization)
<info>./console cache --clear config </info>

Clean lang translation cached files (yaml files and sql texts table)
<info>./console cache --clear lang </info>

Clean SQL cached files (sql cache)
<info>./console cache --clear sql </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $clear  = $input->getOption('clear');
        if ( empty($clear) ) {
           throw new \InvalidArgumentException('Please specify an option for this command!');
        }

        switch ($clear) {
            case 'config':
                $finder = new Finder();
                $finder->files()->in(GOTEO_CACHE_PATH . 'config');

                foreach ($finder as $file) {
                    // Dump the absolute path
                    unlink($file->getRealpath());
                }
                $output->writeln('SQL cache cleared!');
                break;

            case 'lang':
                $finder = new Finder();
                $finder->files()->in(GOTEO_CACHE_PATH . 'config');

                foreach ($finder as $file) {
                    // Dump the absolute path
                    unlink($file->getRealpath());
                }
                $output->writeln('SQL cache cleared!');
                break;

            case 'sql':
                // clear all FyleSystemcache cached files
                FileSystemCache::invalidateGroup();
                $output->writeln('SQL cache cleared!');
                break;

            default:
                throw new \InvalidArgumentException('Option [' . $clear . '] not found!');
                break;
        }


    }
}
