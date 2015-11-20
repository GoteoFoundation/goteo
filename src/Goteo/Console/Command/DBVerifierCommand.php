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

use Goteo\Console\DbVerifier;

/**
 *  Proceso que verifica si los preapprovals han sido coancelados
 *  Solamente trata transacciones paypal pendientes de proyectos en campaña
 *
 *  CRON SUGGESTED LINE:
 *  5 3 * * *       www-data        /usr/bin/php /..path.../bin/console dbverify --update  > /..path.../var/logs/last-cli-dbverify.log
 */
class DBVerifierCommand extends AbstractCommand {

    protected function configure()
    {
        // Old command, old notice hidding
        error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

        $this->setName("dbverify")
             ->setDescription("Paypal preaprova cancelation verifier")
             ->setHelp(<<<EOT
This script verifies some paypal preaproval cancelations

Usage:

...
<info>./console dbverify </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $output->writeln(DbVerifier::process(true));
    }
}
