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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Exception\ProcessFailedException;

use Goteo\Application\Config;
use Goteo\Application\Exception\ModelNotFoundException;
use Goteo\Console\ConsoleEvents;
use Goteo\Console\Event\FilterSendmailEvent;
use Goteo\Console\Event\FilterMailingEvent;

use Goteo\Core\Model;
use Goteo\Model\Mail\SenderRecipient;
use Goteo\Model\Mail;
use Goteo\Model\Mail\Sender;

class SendmailCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("sendmail")
             ->setDescription("Send individual email from the mailer_send table")
             ->setDefinition(array(
                    new InputArgument('sendmail', InputArgument::REQUIRED, 'ID from the mailer_send table. Sends the individual email'),
                    new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                    new InputOption('force', 'f', InputOption::VALUE_NONE, 'Does not check for the message or sendmail to be unlocked before sending'),
                ))
             ->setHelp(<<<EOT
This script processes pending individual emails sendmail and sends it

Usage:

Processes pending sendmail
<info>./console sendmail</info>

Sends pending sendmail 1234 from the mailer_send table:
<info>./console sendmail 1234</info>

EOT
);
    }

    /**
     * Individual email sending
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sendmail = $input->getArgument('sendmail');
        $update = $input->getOption('update');
        $force = $input->getOption('force');

        $recipient = SenderRecipient::get($sendmail);
        if(!$recipient instanceOf SenderRecipient) {
            throw new ModelNotFoundException("ID [$sendmail] not found in mailer_send table!");
        }

        if($recipient->sent) {
            $this->warning("Individual mail already sent!",  [$recipient,  'recipient_user' => $recipient->user]);
            return;
        }

        try {
            if(!$force) {
                // $this->debug("Locking recipient [$recipient->id]",  [$recipient, 'recipient_user' => $recipient->user]);
                if($recipient->isLocked() || !$recipient->setLock(true)->blocked) {
                    throw new \LogicException("Error locking recipient [$recipient->id]");
                }
            }

            $this->info("Sending individual mail",  [$recipient, 'recipient_user' => $recipient->user]);

            if($update) {
                $recipient = $this->dispatch(ConsoleEvents::MAILING_SENDMAIL, new FilterSendmailEvent($recipient, $this->getLogger()))->getRecipient();
                // sleep(1);
            }

        } catch(\LogicException $e) {
            $this->warning('Sendmail Exception', [$recipient, 'recipient_user' => $recipient->user, 'error' =>  $e->getMessage()]);
            return 13;
        }

        if(!$force) {
            // $this->debug("Unlocking recipient [$recipient->id]",  [$recipient, 'recipient_user' => $recipient->user]);
            if($recipient->setLock(false)->blocked) {
                throw new \RuntimeException("Error unlocking recipient [$recipient->id]");
            }
        }

    }
}
