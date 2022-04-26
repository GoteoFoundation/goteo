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
use Goteo\Model\Contract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ContractCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("contract")
             ->setDescription("Manages contracts data")
             ->setDefinition(array(
                      new InputOption('update', 'u', InputOption::VALUE_NONE, 'Actually does the job. If not specified, nothing is done, readonly process.'),
                      new InputOption('contract', 'c', InputOption::VALUE_OPTIONAL, "If specified, checks contract's data")
                ))
             ->setHelp(<<<EOT
This command checks the valid fields for contracts.

Usage:

Update contracts summary data
<info>./console contract --update </info>

Update the provided contract's summary data
<info>./console contract --chanel contract_id --update </info>

EOT
);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $update = $input->getOption('update');
        $contract_id  = $input->getOption('contract');
        $contract_provided = isset($contract_id);

        $output->writeln("<info>Update contracts thrown</info>");

        if ($contract_provided) {
            $output->writeln("<info>Update {$contract_id}'s data </info>");
            $contract = Contract::get($contract_id);
            $contract->validate($errors);
            if ($errors) {
                $this->warning("This contract has invalid data " . implode(',', $errors));

                if(isset($errors['nif'])) {
                    Check::nif($contract->nif, $nif_type);
                    $contract->legal_document_type = $nif_type;
                    $error_save = array();
                    if ($update) {
                        $output->writeln("<info>The contract legal document will be changed to {$nif_type}");
                        if ($contract->save($error_save)) {
                            $output->writeln("<info>The contract legal document has been updated to {$nif_type}</info>");
                            $updated_contracts++;
                        } else {
                            $output->writeln("<error>The contract still has invalid data: " . implode(',', $errors));
                        }
                    } else {
                        $output->writeln("<info>The contract legal document type can be changed to {$nif_type} if used --update");
                    }
                }

            } else {
                $valid_contracts++;
            }
        } else {
            $contracts = Contract::getAll();
            $count = count($contracts);
            $output->writeln("<info>About to treat {$count} contracts </info>");
            $valid_contracts = 0;
            $invalid_contracts = 0;
            $updated_contracts = 0;

            foreach(Contract::getAll() as $contract) {
                $output->writeln("<info>Update {$contract->number} contract from project {$contract->project} </info>");
                $errors = array();
                $contract->validate($errors);

                if (!empty($errors)) {
                    $this->warning("This contract has invalid data " . implode(',', $errors));
                    $invalid_contracts++;

                    if(isset($errors['nif'])) {
                        Check::nif($contract->nif, $nif_type);
                        if ($contract->legal_document_type == '') {
                            $this->warning("The contract had no legal document type defined");
                        }
                        $contract->legal_document_type = $nif_type;
                        $error_save = array();
                        $output->writeln("<info>The contract legal document will be changed to {$nif_type}</info>");
                        if ($update) {
                            if ($contract->save($error_save)) {
                                $output->writeln("<info>The contract legal document has been updated to {$nif_type}</info>");
                                $updated_contracts++;
                            } else {
                                $output->writeln("<error>The contract still has invalid data: " . implode(',', $errors));
                            }
                        } else {
                            $output->writeln("<info>The contract legal document type can be changed to {$nif_type} if used --update");
                        }
                    }
                } else {
                    $valid_contracts++;
                }

                $output->writeln("");
            }

            $output->writeln("<info>Found {$valid_contracts} valid contracts</info>");
            $output->writeln("<info>Found {$invalid_contracts} invalid contracts </info>");
            $output->writeln("<info>Could update {$updated_contracts} invalid contracts</info>");
        }
    }
}
