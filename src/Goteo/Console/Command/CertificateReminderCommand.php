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

use Goteo\Application\Config;
use Goteo\Application\Exception\DuplicatedEventException;
use Goteo\Console\UsersSend;
use Goteo\Model\Event;
use Goteo\Model\User;
use Goteo\Model\User\Donor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class CertificateReminderCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("certificate_reminder")
            ->setDescription("Manages donors reminders")
            ->setDefinition([
                new InputOption('type_of_reminder', '', InputOption::VALUE_REQUIRED, ''),
                new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
            ])
            ->setHelp(<<<EOT
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $typeOfReminder = $input->getOption('type_of_reminder');

        $year = date('Y');
        $update = $input->getOption('update');
        $verbose = $output->isVerbose();
        $very_verbose = $output->isVeryVerbose();
        $verbose_debug = $output->isDebug();

        $total = Donor::getList(['year' => $year, 'show_empty' => true, 'status' => Donor::PENDING], 0, 0, true);
        if (!$total) {
            $this->info("There are no pending donors");
            return;
        }

        $page = 0;
        $limit = 500;
        while ($donors = Donor::getList(['year' => $year, 'show_empty' => true, 'status' => Donor::PENDING], $page * $limit, $limit)) {

            foreach ($donors as $donor) {
                $this->remindToFillDonorData($donor, $typeOfReminder);
            }
            ++$page;
        }
    }

    private function remindToFillDonorData(Donor $donor, string $typeOfReminder) {

        try {
            $action = [$donor->user, $typeOfReminder];
            $event = new Event($action);
        } catch(DuplicatedEventException $e) {
            $this->warning('Duplicated event', ['action' => $e->getMessage(), $donor, 'event' => "$typeOfReminder"]);
            return;
        }

        $event->fire(function() use ($donor, $typeOfReminder) {
            UsersSend::setURL(Config::getUrl(User::getPreferences($donor->user)->comlang));
            UsersSend::toDonors($typeOfReminder, $donor);
        });

        $this->notice("Sent message to $donor->user", [$donor, 'event' => "$typeOfReminder"]);
    }

}
