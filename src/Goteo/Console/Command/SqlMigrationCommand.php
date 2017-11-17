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
use Symfony\Component\Finder\Finder;

use Goteo\Application\Config;

use LibMigration\Migration;
use LibMigration\Config as MigrationConfig;

/**
 * This class integrates LibMigration into the Goteo console/config system
 */
class SqlMigrationCommand extends AbstractCommand {

    protected function configure()
    {
        $this->setName("migrate")
             ->setDescription("Manages SQL version migrations in goteo database")
             ->setDefinition(array(
                    new InputArgument('cmd', InputArgument::OPTIONAL, 'Command to execute. Try --help for info', 'status'),
                    new InputArgument('name', InputArgument::OPTIONAL, 'Name of the task file if command is "create"'),
                    new InputOption('update', 'u', InputOption::VALUE_NONE, 'Executes all migrations pending'),
                    new InputOption('debug', 'd', InputOption::VALUE_NONE, 'Switch the debug mode to output log on the debug level'),
                    new InputOption('config', 'c', InputOption::VALUE_NONE, 'Switch the debug mode to output log on the debug level'),
                ))
             ->setHelp(<<<EOT
LibMigration is a minimum database migration library and framework for MySQL. version 1.1.0

Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0

Usage
  ./bin/console migrate [-h|-d|-c] COMMAND

Options:
  -d         : Switch the debug mode to output log on the debug level.
  -h         : List available command line options (this page).
  -c         : List configurations.
  -u         : Executes all migrations pending

Commands:
  create NAME     : Create new skeleton migration task file.
  status          : List the migrations yet to be executed.
  migrate         : Executes all migrations pending. (same as -u option)
  up              : Executes only the next migration pending.
  down            : Executes the next migration down.

EOT
);
    }

    /**
     * Formats goteo config to LibMigration format
     * @return array config
     */
    protected function getConfig(InputInterface $input, OutputInterface $output) {
        $config = [
            'debug' => $input->getOption('debug'),
            'colors' => $output->isDecorated(),
            'databases' => [
              Config::get('db.database') => [
                  // PDO Connection settings.
                  'database_dsn'      => 'mysql:dbname=' . Config::get('db.database') . ';host=' . Config::get('db.host'),
                  'database_user'     => Config::get('db.username'),
                  'database_password' => Config::get('db.password'),

                  // schema version table
                  'schema_version_table'    => 'schema_version',

                  // directory contains migration task files.
                  'migration_dir'           => GOTEO_PATH . 'db/migrations'
              ]
          ]
        ];

        return $config;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $migration = new Migration($this->getConfig($input, $output));
        $databases = [Config::get('db.database')];

        if($input->getOption('config')) {
            $migration->listConfig();
        }
        elseif('status' === $input->getArgument('cmd')) {
            $migration->status($databases);
        }
        elseif('create' === $input->getArgument('cmd')) {
            if(!$name = $input->getArgument('name')) {
                throw new \InvalidArgumentException("You need to pass the argument for migration task name.");
            }
          $migration ->create($name, $databases);
        }
        elseif('up' === $input->getArgument('cmd')) {
            $migration->up($databases);
        }
        elseif('down' === $input->getArgument('cmd')) {
            $migration->down($databases);
        }
        elseif('create' === $input->getArgument('cmd') || $input->getOption('update')) {
            $migration->migrate($databases);
        }
    }
}
