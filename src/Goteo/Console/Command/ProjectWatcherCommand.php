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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Goteo\Console\ProjectWatcher;

/**
 * Proceso para enviar avisos a los autores segun
 *  - Que lleven 3 meses sin publicar actualizaciones, envia cada mes
 *  - Que lleven 3 meses sin decir nada (?), envia cada 15 dias
 *  - Que hayan pasado dos meses desde que se dio por financiado, cada 15 dias
 *
 *  tiene en cuenta que se envía cada tantos días
 *  CRON SUGGESTED LINE:
 *  5 3 * * *       www-data        /usr/bin/php /..path.../bin/console projectwatch --update  > /..path.../var/logs/last-cli-projectwatch.log
 */
class ProjectWatcherCommand extends AbstractCommand {

    protected function configure()
    {
        // Old command, old notice hidding
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

        $this->setName("projectwatch")
             ->setDescription("Advises to project creators")
             ->setHelp(<<<EOT
This script sends advises and other mails to donors and creators of projects

Usage:

...
<info>./console projectwatch </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $output->writeln(ProjectWatcher::process(true));
    }
}
