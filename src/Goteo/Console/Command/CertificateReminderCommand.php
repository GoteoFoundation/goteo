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

use Goteo\Library\Check;
use Goteo\Model\User\Donor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CertificateReminderCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("certificate_reminder")
            ->setDescription("Manages donors reminders")
            ->setDefinition(array(
                new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
            ))
            ->setHelp(<<<EOT
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');
        $verbose = $output->isVerbose();
        $very_verbose = $output->isVeryVerbose();
        $verbose_debug = $output->isDebug();
    }
}
