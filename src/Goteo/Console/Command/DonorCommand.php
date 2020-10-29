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
<info>./console donor --update </info>

Update the provided donor's summary data
<info>./console donor --donor donor_id --update </info>

Update the provided user's donor amount
<info>./console donor --update_amounts --user user_id --update </info>

Update the donor amounts for a year
<info>./console donor --update_amounts --year 2020 --update </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $update = $input->getOption('update');
        $verbose = $output->isVerbose();

        $update_donors = $input->getOption('update_donors');
        $update_amounts = $input->getOption('update_amounts');
        $update_status = $input->getOption('update_status');

        if ($update_amounts) {
            $user = $input->getOption('user');
            $year = $input->getOption('year')? $input->getOption('year') : date('Y');
        }

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
                                    $output->writeln("<error>The donor still has invalid data: " . implode(',', $errors));
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
                'status' => Donor::DATA_FILLED,
                'show_empty' => true,
            ];

            if ($user) {
                $filter['user'] = $user;
            }

            $status_to_filter = Donor::$DONOR_STATUSES;

            $updated_donors = 0;
            $updated_status_donors = 0;
            $donors_with_errors = 0;

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

                        // $pending_investions = Donor::getPendingInvestions($donor->user);

                        // $donor_year = $donor->year;
                        // if (!empty($pending_investions)) {
                        //     $missing_investions = count($pending_investions);
                        //     $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} has {$missing_investions} invest to be added to the certificate.</info>");
                        //     $updated_donors++;
                        //     if ($update) {
                        //         $donor->updateInvestions();
                        //     }
                        // }

                        $donor_year = $donor->year;
                        $invest_filters = [
                            'status' => [Invest::STATUS_CHARGED, Invest::STATUS_PAID, Invest::STATUS_TO_POOL, Invest::STATUS_DONATED],
                            'date_from' => $donor_year . '-01-01',
                            'date_until' => $donor_year . '-12-31',
                            'users' => $donor->user,
                            'procStatus' => 'passed'
                        ];

                        $user_invests = Invest::getList($invest_filters, null, 0, 0, 'all');
                        $donor_amount = $user_invests['amount'] + $user_invests['donations_amount'];

                        if ($donor_amount != $donor->amount) {
                            if ($verbose) {
                                $progress_bar->clear();
                                $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} has {$donor_amount} but only {$donor->amount} in the certificate.</info>");
                                $progress_bar->display();
                            }

                            $errors = [];

                            if ($donor->validateData($errors)) {
                                $updated_donors++;
                                if ($update) {
                                    $donor->updateInvestions();
                                    if ($donor->status == Donor::PENDING) {
                                        $donor->status = Donor::COMPLETED;
                                        $donor->completed = date('Y-m-d H:i:s');
                                        $donor->save();
    
                                        $updated_status_donors++;
                                    }
                                }
                            } else {
                                $donors_with_errors++;
                                if ($verbose) {
                                    $progress_bar->clear();
                                    $output->writeln("<warning> {$donor->id} - {$donor->name} - {$donor->nif} </warning>");
                                    $output->writeln("<warning> ". implode(',',$errors) . "</warning>");
                                    $progress_bar->display();
                                }
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
                $output->writeln("<info>A total of {$donors_with_errors} out of {$total_donors} have errors.</info>");
                $output->writeln("<info>A total of {$updated_donors} out of {$total_donors} have been updated.</info>");
                $output->writeln("<info>A total of {$updated_status_donors} out of {$total_donors} have the status changed to completed.</info>");

            } else {
                $output->writeln("<info>A total of {$donors_with_errors} out of {$total_donors} have errors.</info>");
                $output->writeln("<info>A total of {$updated_donors} out of {$total_donors} can be updated using --update");
            }

        } else if ($update_status) {
            $filter = [
                'year' => $year,
                'status' => Donor::DATA_FILLED
            ];

            if ($user) {
                $filter['user'] = $user;
            }

            $status = Donor::PENDING;

            $updated_donors = 0;
            $donors_with_errors = 0;

            $offset = 0;
            $limit = 100;
            $total_donors = 0;

            $output->writeln("<info>About to treat donors with {$status} status </info>");

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

                        $donor_year = $donor->year;

                        if ($donor->amount != 0) {
                            $errors = [];
                            if ($donor->validateData($errors)) {
                                if ($verbose) {
                                    $progress_bar->clear();
                                    $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} can change it's status .</info>");
                                    $progress_bar->display();
                                }
                                $updated_donors++;
                                if ($update) {
                                    $errors = [];
                                    $donor->status = Donor::COMPLETED;
                                    $donor->completed = date('Y-m-d H:i:s');
                                    if (!$donor->save($errors)) {   
                                        if ($verbose) {
                                            $progress_bar->clear();
                                            $output->writeln("<warning> {$donor->id} - {$donor->name} - {$donor->nif} </warning>");
                                            $output->writeln("<warning> ". implode(',',$errors) . "</warning>");
                                            $progress_bar->display();
                                        }
                                    } else {
                                        if ($verbose) {
                                            $progress_bar->clear();
                                            $output->writeln("<info>Update {$donor->id} - {$donor->name} - {$donor->nif} can change it's status .</info>");
                                            $progress_bar->display();
                                        }
                                    }
                                }
                            } else {
                                $donors_with_errors++;
                                if ($verbose) {
                                    $progress_bar->clear();
                                    $output->writeln("<warning> {$donor->id} - {$donor->name} - {$donor->nif} </warning>");
                                    $output->writeln("<warning> ". implode(',',$errors) . "</warning>");
                                    $progress_bar->display();
                                }
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

            } else {
                $output->writeln("<info>A total of {$updated_donors} out of {$total_donors} can be updated using --update");
                $output->writeln("<info>A total of {$donors_with_errors} out of {$total_donors} have errors.</info>");
            }
        }

    }
}
