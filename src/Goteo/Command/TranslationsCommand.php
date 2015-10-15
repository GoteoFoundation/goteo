<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */

namespace Goteo\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

use Symfony\Component\Yaml\Yaml;

use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Text;


class TranslationsCommand extends Command {

    protected function configure()
    {
        $lang = Config::get('lang');

        $this->setName("lang")
             ->setDescription("Manages Texts and Translations used on Goteo")
             ->setDefinition(array(
                      new InputOption('lang', 'l', InputOption::VALUE_OPTIONAL, 'Lang to import (ISO 639-1 codes) (default: defined in settings)', $lang),
                      new InputOption('langs', 'L', InputOption::VALUE_NONE, 'List available langs'),
                      new InputOption('dump', null, InputOption::VALUE_NONE, 'Dumps the content of the language into Resources/translations/{LANG}/{GROUP}.yml'),
                      new InputOption('sql', null, InputOption::VALUE_NONE, 'Retrieves only SQL content on specified language'),
                      new InputOption('sql-clear', null, InputOption::VALUE_NONE, 'Deletes all entries from the text SQL table on specified language'),
                      new InputOption('full', null, InputOption::VALUE_NONE, 'Show text from fallback language if not defined in current'),
                ))
             ->setHelp(<<<EOT
This command may be used to export texts from database into YAML files

Usage:

With no arguments, the content of the 'text' for lang specified (from settings if not defined)
<info>./console lang </info>

Listing available langs (YAML format)
<info>./console lang --langs </info>

Shows translation content for some lang
<info>./console lang  -l en|fr|es|de|...</info>

Shows translation content for some lang stored only in local database (excludes yaml files)
<info>./console lang --sql -l en|fr|es|de|...</info>

WRITES YAML files into Resources/translations/[LANG]/[GROUP].yml for lang specified in settings
<info>./console lang --dump </info>

WRITES YAML files into Resources/translations/[LANG]/[GROUP].yml for lang English
<info>./console lang --dump -l en</info>

DELETES sql entries for lang specified that are already defined into yaml files
<info>./console lang --sql-clear -l en|fr|es|de|...</info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if($input->getOption('langs')) {
          $output->writeln(Yaml::dump(Lang::listAll('array', false)));
          return;
        }

        $lang = $input->getOption('lang');
        $dump  = $input->getOption('dump');
        $full  = $input->getOption('full');
        $sql  = $input->getOption('sql');
        $sqlclear  = $input->getOption('sql-clear');

        if ( empty($lang) ) {
           throw new \InvalidArgumentException('No lang defined. Please define it in settings or by using --lang option');
        }
        // check lang availability
        Lang::set($lang);
        $newlang = Lang::current();
        if($newlang !== $lang) {
           throw new \InvalidArgumentException('The lang ['.$lang.'] is not used. Please define a valid language in settings or by using --lang option');
        }

        if($sqlclear) {
            $files = Text::getAll(['filesonly' => 1, 'strict' => 1], $lang);
            $sql = Text::getAll(['sqlonly' => 1, 'strict' => 1], $lang);
            foreach($sql as $t) {
                // check key availability on yml before delete files
                if(!isset($files[$t->id])) {
                   $output->writeln("<error>Error: </error> key <comment>{$t->id}</comment> cannot be deleted because is not found on translation files!");
                   continue;
                }
                Text::delete($t->id, $lang);
                $output->writeln("Deleted key <info>{$t->id}</info> from SQL text database");
            }
            return;
        }

        foreach(Lang::groups() as $group => $parts) {
            $filter = ['group' => $group];
            if(!$full) {
                $filter['strict'] = 1;
            }
            if($sql) {
                $filter['sqlonly'] = 1;
            }
            $all = Text::getAll($filter, $lang);

            if(empty($all)) continue;
            $output->writeln("<info>$group</info>");

            $texts = [];
            foreach($all as $text) {
                $texts[$text->id] = $text->text;
            }
            $yml = Yaml::dump($texts);
            if($dump) {
                // Main dir
                $dir = __DIR__ . '/../../../Resources/translations/' . $lang . '/';
                mkdir($dir, 0755, true);
                file_put_contents($dir . $group . '.yml', $yml);
                $output->writeln("<comment>Dumped Lang collection into $dir$group.yml</comment>");
            }
            $output->writeln($yml);
        }

        $output->writeln("Using lang: <comment>$lang</comment>");
    }
}
