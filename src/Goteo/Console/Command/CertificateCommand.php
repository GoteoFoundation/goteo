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
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;


use Goteo\Application\Config;
use Goteo\Application\Lang;
use Goteo\Library\Check;
use Goteo\Library\Text;
use Goteo\Model\LegalDocumentType;
use Goteo\Model\User\Donor;
use Goteo\Model\Invest;
use FileSystemCache;


class CertificateCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("certificate")
             ->setDescription("Manages donors data")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                      new InputOption('update_donors', 'c', InputOption::VALUE_NONE, "If specified, checks all donor's data"),
                      new InputOption('donor', 'd', InputOption::VALUE_OPTIONAL, "If specified, checks donor's data"),
                      new InputOption('update_amounts', 'a', InputOption::VALUE_NONE, "If specified calculates new amounts for donors"),
                      new InputOption('update_status', 's', InputOption::VALUE_NONE, "If specified updates the status of the donors"),
                      new InputOption('user', 'usr', InputOption::VALUE_OPTIONAL, "If specified used to search donations from a user" ),
                      new InputOption('year', 'y', InputOption::VALUE_OPTIONAL, "If specified used to search for donors of the selected year, if not current year is used")
                ))
             ->setHelp(<<<EOT
This command checks the valid fields for donors.

Usage:

Update donors summary data
<info>./console donor --update_donors --update </info>

Update the provided donor's summary data
<info>./console donor --update_donors --donor donor_id --update </info>

Update the donors amount
<info>./console donor --update_amounts </info>

Update the provided user's donor amount
<info>./console donor --update_amounts --user user_id --update </info>

Update the donors amounts for a given year
<info>./console donor --update_amounts --year 2020 --update </info>

Update the provided user's donor status
<info>./console donor --update_status --user user_id --update </info>

Update the provided user's donor status
<info>./console donor --update_status --user user_id --update </info>

Update the donors status for a year
<info>./console donor --update_status --year 2020 --update </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        error_reporting(0);
        $update = $input->getOption('update');
        $verbose = $output->isVerbose();
        $very_verbose = $output->isVeryVerbose();
        $verbose_debug = $output->isDebug();


        $update_donors = $input->getOption('update_donors');
        $update_amounts = $input->getOption('update_amounts');
        $update_status = $input->getOption('update_status');

        if(!$update_donors && !$update_amounts && !$update_status) {
            throw new \InvalidArgumentException('No action defined. Please define any action with --update_donors, --update_amounts or --update_status');
        }

        $user = $input->getOption('user');
        $year = $input->getOption('year')? $input->getOption('year') : date('Y');

        $output->writeln("<info>Update donors thrown</info>");

        if ($update_donors) {
            $donor  = $input->getOption('donor');
            if ($donor) {
                $output->writeln("<info>Update {$donor}'s data </info>");
                $donor = Donor::get($donor);
                $donor->validateData($errors);
                if ($errors) {
                    $this->warning("This donor has invalid data " . implode(',', $errors));

                    $valid_nif = Check::nif($donor->nif, $nif_type);

                    if (isset($errors['legal_entity'])) {
                        $output->writeln("<info>The donor has not specified the legal entity</info>");
                        $output->writeln("<info>Check proves the nif to be from a " . $nif_type);
                        if ($nif_type == LegalDocumentType::CIF) {
                            $output->writeln("<info>The donor legal entity will be changed to " . LegalEntity::LEGAL_PERSON);
                            $donor->legal_entity = LegalEntity::LEGAL_PERSON;
                        } else {
                            $output->writeln("<info>The donor legal entity will be changed to " . LegalEntity::NATURAL_PERSON);
                            $donor->legal_entity = LegalEntity::NATURAL_PERSON;
                        }
                    }

                    if(isset($errors['nif'])) {
                        if ($valid_nif && $nif_type != LegalDocumentType::VAT) {
                            $donor->legal_document_type = $nif_type;
                            $error_save = array();
                            if ($update) {
                                $output->writeln("<info>The donor legal document will be changed to {$nif_type}");
                                if ($donor->save($error_save)) {
                                    $output->writeln("<info>The donor legal document has been updated to {$nif_type}</info>");
                                    $updated_donors++;
                                } else {
                                    $output->writeln("<error>The donor still has invalid data: " . implode(',', $errors));
                                }
                            } else {
                                $output->writeln("<info>The donor legal document type can be changed to {$nif_type} if used --update");
                            }
                        }
                    }

                } else {
                    $output->writeln("<info>The donor has no errors in its data</info>");
                }
            } else {
                $filter = [
                    'show_empty' => true
                ];

                if ($input->getOption('year')) {
                    $filter['year'] = $input->getOption('year');
                }

                $count_donors = Donor::getList($filter, 0, 0, true);
                $donors = Donor::getList($filter, 0, $count_donors);
                $output->writeln("<info>About to treat {$count_donors} donors </info>");

                $progress_bar = new ProgressBar($output, $count_donors);
                $progress_bar->start();
                
                $valid_donors = 0;
                $invalid_donors = 0;
                $updated_donors = 0;
                $could_not_update = 0;
                $updated = 0;
                $invalid_nif = 0;
                $cant_update = 0;

                foreach($donors as $donor) {
                    if ($verbose) {
                        $progress_bar->clear();
                        $output->writeln("<info>Update  {$donor->id} - {$donor->name} - {$donor->nif} donor</info>");
                        $progress_bar->display();
                    }

                    $errors = array();
                    $can_be_updated = false;
                    $donor->validateData($errors);

                    if (!empty($errors)) {

                        if ($verbose) {
                            $progress_bar->clear();
                            $this->warning("This donor has invalid data " . implode(',', $errors));
                            $progress_bar->display();
                        }

                        $invalid_donors++;
                        $valid_nif = Check::nif($donor->nif, $nif_type);

                        if(isset($errors['nif'])) {
                            
                            if ($valid_nif && $nif_type != LegalDocumentType::VAT) {
                                $donor->legal_document_type = $nif_type;
                                 if ($nif_type == LegalDocumentType::CIF) {
                                    $donor->legal_entity = LegalEntity::LEGAL_PERSON;
                                } else {
                                    $donor->legal_entity = LegalEntity::NATURAL_PERSON;
                                }
                                $can_be_updated = true;

                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $output->writeln("<info>The donor legal document type can be changed to {$nif_type} and the legal entity to {$donor->legal_entity} if used --update </info>");
                                    $progress_bar->display();
                                }        
                            } else {
                                $invalid_nif++;
                            }
                        }

                        if (isset($errors['legal_entity'])) {

                            if ($verbose) {
                                $progress_bar->clear();
                                $output->writeln("<info>The donor has not specified the legal entity or there is a mismatch between legal entity and type of document</info>");
                                $progress_bar->display();
                            }

                            if ($valid_nif) {
                                if ($nif_type == LegalDocumentType::CIF) {
                                    if ($verbose) {
                                        $progress_bar->clear();        
                                        $output->writeln("<info>The donor legal entity will be changed to " . LegalEntity::LEGAL_PERSON . "</info>");
                                        $progress_bar->display();
                                    }

                                    $donor->legal_entity = LegalEntity::LEGAL_PERSON;
                                    $can_be_updated = true;
                                } else {
                                    if ($verbose) {
                                        $progress_bar->clear();
                                        $output->writeln("<info>The donor legal entity will be changed to " . LegalEntity::NATURAL_PERSON . "</info>");
                                        $progress_bar->display();
                                    }

                                    $donor->legal_entity = LegalEntity::NATURAL_PERSON;
                                    $can_be_updated = true;
                                }
                            }
                        }

                        if (isset($errors['legal_document_type'])) {

                            if ($verbose) {
                                $progress_bar->clear();
                                $output->writeln("<info>The donor has not specified the legal document type</info>");
                                $output->writeln("<info>The donor legal entity will be changed to " . $nif_type . "</info>");
                                $progress_bar->display();
                            }

                            if ($valid_nif && $nif_type != LegalDocumentType::VAT) {
                                $donor->legal_document_type = $nif_type;
                                $can_be_updated = true;

                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $output->writeln("<info>The donor legal document type can be changed to {$nif_type} and the legal entity to {$donor->legal_entity} if used --update </info>");
                                    $progress_bar->display();
                                }
                            }
                        }

                        $errors = array();
                        $valid = $donor->validateData($errors);

                        if ($can_be_updated && $valid) {
                            $updated_donors++;
                        } else if ($can_be_updated && !$valid) {
                            $could_not_update++;
                        } else{
                            $cant_update++;
                        }

                        if ($update && $can_be_updated && $valid) {
                            $error_save = array();
                            if ($donor->save($error_save)) {
                                $updated++;
                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $output->writeln("<info>The donor legal document has been updated</info>");
                                    $progress_bar->display();
                                }
                            } else {
                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $this->warning("This donor still has invalid data " . implode(',', $error_save));
                                    $progress_bar->display();
                                }
                            }
                        }

                    } else {
                        $valid_donors++;
                    }

                    $progress_bar->advance();
                }

                $progress_bar->finish();
                $output->writeln("");

                $output->writeln("<info>Found {$valid_donors} valid donors</info>");
                $output->writeln("<info>Found {$invalid_donors} invalid donors </info>");
                $output->writeln("<info>Found {$invalid_nif} invalid donors with invalid nif</info>");
                $output->writeln("<info>Can update {$updated_donors} donors entity or document type.</info>");
                $output->writeln("<info>Can NOT update  {$could_not_update} invalid donors that have changes due to validation</info>");
                $output->writeln("<info>Can NOT make changes to {$cant_update} invalid donors </info>");
                $output->writeln("<info>{$updated} invalid donors HAVE been updated</info>");

            }
        } else if ($update_amounts) {

            $filter = [
                'year' => $year,
                'show_empty' => true,
                'status' => Donor::UNCONFIRMED
            ];

            if ($user) {
                $filter['user'] = $user;
            }

            $status_to_filter = Donor::$DONOR_STATUSES;

            $updated_donors = 0;
            $can_be_updated = 0;

            $offset = 0;
            $limit = 100;
            $total_donors = 0;
            $update_errors = 0;

            foreach ($status_to_filter as $status) {
                $output->writeln("<info>About to treat donors with {$status} status </info>");

                $filter['donor_status'] = $status;
                $total = Donor::getList($filter, 0, 0, true);
                if (!$total) {
                    $output->writeln("<info>There are no donors in this state</info>");
                    continue;
                }

                $output->writeln("<info>About to treat {$total} donors </info>");

                $progress_bar = new ProgressBar($output, $total);
                $progress_bar->start();
                $donors = Donor::getList($filter, 0, $total);

                foreach($donors as $donor) {

                    $user_invests = Donor::getPendingInvestionsAmount($donor->user, $year);
                    $donor_amount = $user_invests['amount'] + $user_invests['donations_amount'];

                    if ($donor_amount != 0 || $donor->amount != $donor->getAmount()) {
                        if ($verbose_debug) {
                            $progress_bar->clear();
                            $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} has {$donor_amount} but only {$donor->amount} in the certificate.</info>");
                            $progress_bar->display();
                        }

                        $errors = [];

                        if ($update) {
                            if ($donor->updateInvestions()) {
                                $updated_donors++;
                            } else {
                                $update_errors++;
                            }
                        } else {
                            $can_be_updated++;
                        }
                    }
                    $progress_bar->advance();
                }

                $total_donors += $total;

                $progress_bar->finish();
                $output->writeln('');
            }

            if ($update) {
                $output->writeln("<info>A total of {$updated_donors} out of {$total_donors} have been updated.</info>");
                $output->writeln("<info>A total of {$update_errors} out of {$total_donors} have errors.</info>");
            } else {
                $output->writeln("<info>A total of {$can_be_updated} out of {$total_donors} can be updated using --update");
            }

        } else if ($update_status) {
            
            $filter = [
                'year' => $year,
                'report' => true,
                'donor_status' => Donor::PENDING
            ];

            if ($user) {
                $filter['user'] = $user;
            }

            $can_be_updated = 0;
            $updated_donors = 0;
            $donors_with_errors = 0;
            $donor_without_amount = 0;
            $donors_valid_and_amount = 0;
            $donors_valid_without_amount = 0;
            $donors_treated = 0;
            $donors_invalid_and_amount = 0;

            $offset = 0;
            $limit = 100;
            $total_donors = 0;

            $output->writeln("<info>About to treat donors with pending status and data filled </info>");

            $total = Donor::getList($filter, 0, 0, true);
            if (!$total) {
                $output->writeln("<info>There are no donors in this state</info>");
            } else {
                $output->writeln("<info>About to treat {$total} donors </info>");

                $progress_bar = new ProgressBar($output, $total);
                $progress_bar->start();

                while ($donors = Donor::getList($filter, $offset, $limit)) {
                    
                    foreach($donors as $donor) {
                        ++$donors_treated;

                        if ($donor->status != Donor::PENDING)
                            continue;
                            
                        $errors = [];
                        $is_valid = $donor->validateData($errors);
                        
                        if ($is_valid) {
                            $can_be_updated++;
                            if ($verbose || $verbose_debug) {
                                $progress_bar->clear();
                                $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} can change it's status .</info>");
                                $progress_bar->display();
                            }
                            
                        } else {
                            $donors_with_errors++;
                            if ($verbose_debug) {
                                $progress_bar->clear();
                                $output->writeln("<error> {$donor->id} - {$donor->name} - {$donor->nif}. The donor still has invalid data.</error>");
                                $progress_bar->display();
                            }
                        }

                        if ($donor->amount != 0) {
                            if ($is_valid) {
                                $donors_valid_and_amount++;

                                if ($update) {
                                    $errors = [];
                                    $donor->status = Donor::COMPLETED;
                                    $donor->completed = date('Y-m-d H:i:s');
                                    if ($donor->save($errors)) {   
                                        $updated_donors++;
                                        if ($verbose_debug) {
                                            $progress_bar->clear();
                                            $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} has changed it's status .</info>");
                                            $progress_bar->display();
                                        }
                                    } else {
                                        if ($verbose || $verbose_debug) {
                                            $progress_bar->clear();
                                            $output->writeln("<error> {$donor->id} - {$donor->name} - {$donor->nif}. ".  implode(',', $errors)  . "</error>");
                                            $progress_bar->display();
                                        }
                                    }
                                }
                            } else {
                                $donors_invalid_and_amount++;
                            }
                        } else {
                            $donor_without_amount++;
                            if ($is_valid) {
                                $donors_valid_without_amount++;
                            }
                        }

                        $progress_bar->advance();
                    }

                    $offset+=$limit;
                }
                $progress_bar->finish();
                $total_donors += $total;
                $output->writeln('');
            }

            if ($update) {
                $output->writeln("<info>A total of {$updated_donors} out of {$total_donors} have been updated.</info>");
                $output->writeln("<info>A total of {$donors_with_errors} out of {$total_donors} have errors.</info>");
                $output->writeln("<info>A total of {$donor_without_amount} out of {$total_donors} have no amount.</info>");

            } else {
                $output->writeln("<info>A total of {$donors_treated} out of {$total_donors} have been treated");
                $output->writeln("<info>A total of {$can_be_updated} out of {$total_donors} can be updated using --update");
                $output->writeln("<info>A total of {$donors_with_errors} out of {$total_donors} have errors.</info>");
                $output->writeln("<info>A total of {$donor_without_amount} out of {$total_donors} have no amount.</info>");
                $output->writeln("<info>A total of {$donors_valid_and_amount} out of {$total_donors} is valid and has amount.</info>");
                $output->writeln("<info>A total of {$donors_valid_without_amount} out of {$total_donors} is valid but has no amount.</info>");
            }
        }

    }
}
