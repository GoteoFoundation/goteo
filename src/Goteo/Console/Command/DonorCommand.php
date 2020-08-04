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
use Goteo\Library\Check;
use Goteo\Library\Text;
use Goteo\Model\User\Donor;
use FileSystemCache;


class DonorCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("donor")
             ->setDescription("Manages donors data")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                      new InputOption('donor', 'd', InputOption::VALUE_OPTIONAL, "If specified, checks donor's data")
                ))
             ->setHelp(<<<EOT
This command checks the valid fields for donors.

Usage:

Update donors summary data
<info>./console donor --update </info>

Update the provided donor's summary data
<info>./console donor --donor donor_id --update </info>


EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $update = $input->getOption('update');
        $donor_id  = $input->getOption('donor');
        $donor_provided = isset($donor_id);

        $output->writeln("<info>Update donors thrown</info>");

        if ($donor_provided) {
            $output->writeln("<info>Update {$donor_id}'s data </info>");
            $donor = Donor::get($donor_id);
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
            $count_donors = Donor::getList([], 0, 0, true);
            $donors = Donor::getList([], 0, $count_donors);
            $output->writeln("<info>About to treat {$count_donors} donors </info>");
            $valid_donors = 0;
            $invalid_donors = 0;
            $updated_donors = 0;

            foreach($donors as $donor) {
                $output->writeln("<info>Update {$donor->name} donor</info>");
                $errors = array();
                $donor->validateData($errors);

                if (!empty($errors)) {
                    $this->warning("This donor has invalid data " . implode(',', $errors));
                    $invalid_donors++;

                    if (isset($errors['legal_entity'])) {
                        $output->writeln("<info>The donor has not specified the legal entity</info>");
                        Check::nif($donor->nif, $nif_type);
                        $output->writeln("<info>Check proves the nif to be from a " . $nif_type);
                        if ($nif_type == Donor::CIF) {
                            if ($update) {
                                $output->writeln("<info>The donor legal entity will be changed to " . Donor::LEGAL_PERSON);
                                $donor->legal_entity = Donor::LEGAL_PERSON;
                            } else {

                                $output->writeln("<info>The donor legal entity could be changed to " . Donor::LEGAL_PERSON);
                            }
                        } else {
                            if ($update) {
                                $output->writeln("<info>The donor legal entity will be changed to " . Donor::NATURAL_PERSON);
                                $donor->legal_entity = Donor::NATURAL_PERSON;
                            } else {
                                $output->writeln("<info>The donor legal entity could be changed to " . Donor::NATURAL_PERSON);
                            }
                        }
                    }
    
                    if(isset($errors['nif'])) {
                        Check::nif($donor->nif, $nif_type);
                        if ($donor->legal_document_type == '') {
                            $this->warning("The donor had no legal document type defined");
                        }
                        $donor->legal_document_type = $nif_type;
                        $error_save = array();
                        if ($update)
                            $output->writeln("<info>The donor legal document will be changed to {$nif_type}</info>");
                    }

                    if ($update) {
                        $error_save = array();
                        if ($donor->save($error_save)) {
                            $output->writeln("<info>The donor legal document has been updated to {$nif_type}</info>");
                            $updated_donors++;
                        } else {
                            $output->writeln("<error>The donor still has invalid data: " . implode(',', $errors));
                        }
                    } else {
                        $output->writeln("<info>The donor legal document type can be changed to {$nif_type} and the legal entity to {$donor->legal_entity} if used --update");
                    }

                } else {
                    $valid_donors++;
                }

                $output->writeln("");
            }

            $output->writeln("<info>Found {$valid_donors} valid donors</info>");
            $output->writeln("<info>Found {$invalid_donors} invalid donors </info>");
            $output->writeln("<info>Could update {$updated_donors} invalid donors</info>");

        }
    }
}
