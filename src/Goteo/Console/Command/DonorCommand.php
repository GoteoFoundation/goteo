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
use Goteo\Model\User\Donor;
use Goteo\Model\Invest;
use FileSystemCache;


class DonorCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("donor")
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

                    if (isset($errors['legal_entity'])) {
                        $output->writeln("<info>The donor has not specified the legal entity</info>");
                        Check::nif($donor->nif, $nif_type);
                        $output->writeln("<info>Check proves the nif to be from a " . $nif_type);
                        if ($nif_type == Donor::CIF) {
                            $output->writeln("<info>The donor legal entity will be changed to " . Donor::LEGAL_PERSONA);
                            $donor->legal_entity = Donor::LEGAL_PERSON;
                        } else {
                            $output->writeln("<info>The donor legal entity will be changed to " . Donor::NATURAL_PERSON);
                            $donor->legal_entity = Donor::NATURAL_PERSON;
                        }
                    }

                    if(isset($errors['nif'])) {
                        Check::nif($donor->nif, $nif_type);
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

                } else {
                    $valid_donors++;
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

                foreach($donors as $donor) {
                    if ($verbose) {
                        $progress_bar->clear();
                        $output->writeln("<info>Update  {$donor->id} - {$donor->name} - {$donor->nif} donor</info>");
                        $progress_bar->display();
                    }

                    $errors = array();
                    $donor->validateData($errors);

                    if (!empty($errors)) {
                        if ($verbose) {
                            $progress_bar->clear();
                            $this->warning("This donor has invalid data " . implode(',', $errors));
                            $progress_bar->display();
                        }

                        $invalid_donors++;

                        if (isset($errors['legal_entity'])) {

                            Check::nif($donor->nif, $nif_type);
                            

                            if ($verbose) {
                                $progress_bar->clear();
                                $output->writeln("<info>The donor has not specified the legal entity</info>");
                                $progress_bar->display();

                                $progress_bar->clear();
                                $output->writeln("<info>Check proves the nif to be from a " . $nif_type);
                                $progress_bar->display();
                            }

                            if ($nif_type == Donor::CIF) {
                                if ($update) {
                                    $progress_bar->clear();
                                    $output->writeln("<info>The donor legal entity will be changed to " . Donor::LEGAL_PERSON);
                                    $progress_bar->display();
                                    $donor->legal_entity = Donor::LEGAL_PERSON;
                                } else {
                                    if ($verbose) {
                                        $progress_bar->clear();        
                                        $output->writeln("<info>The donor legal entity could be changed to " . Donor::LEGAL_PERSON);
                                        $progress_bar->display();
                                    }
                                }
                            } else {
                                if ($update) {
                                    $progress_bar->clear();
                                    $output->writeln("<info>The donor legal entity will be changed to " . Donor::NATURAL_PERSON);
                                    $progress_bar->display();

                                    $donor->legal_entity = Donor::NATURAL_PERSON;
                                } else {
                                    if ($verbose) {
                                        $progress_bar->clear();
                                        $output->writeln("<info>The donor legal entity could be changed to " . Donor::NATURAL_PERSON);
                                        $progress_bar->display();
                                    }
                                }
                            }
                        }
        
                        if(isset($errors['nif'])) {
                            Check::nif($donor->nif, $nif_type);
                            if ($donor->legal_document_type == '') {
                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $this->warning("The donor had no legal document type defined");
                                    $progress_bar->display();
                                }
                            }
                            $donor->legal_document_type = $nif_type;
                            $error_save = array();
                            if ($update) {
                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $output->writeln("<info>The donor legal document will be changed to {$nif_type}</info>");
                                    $progress_bar->display();
                                }
                            }
                        }

                        if ($update) {
                            $error_save = array();
                            if ($donor->save($error_save)) {
                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $output->writeln("<info>The donor legal document has been updated to {$nif_type}</info>");
                                    $progress_bar->display();
                                }
                                $updated_donors++;
                            } else {
                                if ($verbose) {
                                    $progress_bar->clear();        
                                    $output->writeln("<error>The donor still has invalid data.");
                                    $progress_bar->display();
                                }
                            }
                        } else {
                            if ($verbose) {
                                $progress_bar->clear();        
                                $output->writeln("<info>The donor legal document type can be changed to {$nif_type} and the legal entity to {$donor->legal_entity} if used --update");
                                $progress_bar->display();
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
                $output->writeln("<info>Could update {$updated_donors} invalid donors</info>");

            }
        } else if ($update_amounts) {

            $filter = [
                'year' => $year,
                'show_empty' => true,
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

                while ($donors = Donor::getList($filter, $offset, $limit)) {
                    
                    foreach($donors as $donor) {

                        $donor_year = $donor->year;
                        $invest_filters = [
                            'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_TO_POOL, Invest::STATUS_DONATED],
                            'date_from' => date_format(date_sub(date_create($donor_year . '-01-01'), date_interval_create_from_date_string('40 days')), 'Y-m-d'),
                            'date_until' => $donor_year . '-12-31',
                            'users' => $donor->user,
                            'procStatus' => 'passed'
                        ];

                        // $user_invests = Invest::getList($invest_filters, null, 0, 0, 'all');
                        $user_invests = Donor::getPendingInvestionsAmount($donor->user);
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
                                }
                            } else {
                                $can_be_updated++;
                            }
                            // if ($verbose_debug)
                                // print_r($donor);

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

            } else {
                $output->writeln("<info>A total of {$can_be_updated} out of {$total_donors} can be updated using --update");
            }

        } else if ($update_status) {
            
            $filter = [
                'year' => $year
            ];

            if ($user) {
                $filter['user'] = $user;
            }

            $status = Donor::PENDING;

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

            $filter['donor_status'] = $status;
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

                        $donor_year = $donor->year;

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
                                // print_r($donor);
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
