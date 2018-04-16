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
use Goteo\Core\Model;

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
                    new InputOption('update', 'u', InputOption::VALUE_NONE, 'Executes all pending migrations'),
                    new InputOption('debug', 'd', InputOption::VALUE_NONE, 'Switch the debug mode to output log on the debug level'),
                    new InputOption('config', 'c', InputOption::VALUE_NONE, 'List configurations'),
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
  -u         : Executes all pending migrations

Commands:
  create NAME     : Create new skeleton migration task file.
  status          : List the migrations yet to be executed (same as non options).
  all             : Executes all pending migrations. (same as -u option)
  up              : Executes only the next pending migration up.
  down            : Executes the next migration down (reverts to a previous state).
  install         : Installs the system from the scratch (only works on empty databases)

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
                  'database_dsn'      => 'mysql:dbname=' . Config::get('db.database') . ';host=' . Config::get('db.host') . ';charset=UTF8',
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
        elseif('all' === $input->getArgument('cmd') || $input->getOption('update')) {
            $migration->migrate($databases);
        }
        elseif('up' === $input->getArgument('cmd')) {
            $migration->up($databases);
        }
        elseif('down' === $input->getArgument('cmd')) {
            $migration->down($databases);
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
        elseif('install' === $input->getArgument('cmd')) {
            $res = Model::query("select count(*) from information_schema.tables where table_type = 'BASE TABLE' and table_schema = ?", $databases[0]);
            if($res->fetchColumn()) throw new \Exception("Database {$databases[0]} is not empty! Cannot install\n\nYou may want to execute:\nphp bin/console migrate all");

            $output->writeln("<comment>Installing database structure into <info>[{$databases[0]}]</info> ...</comment>");
            Model::query(file_get_contents(GOTEO_PATH . 'db/migrations/init-v3.2.sql'));
            $output->writeln('<info>Done</info>');

            $output->writeln('<comment>Installing initial data...</comment>');
            Model::query(file_get_contents(GOTEO_PATH . 'db/migrations/data-v3.2.sql'));
            $output->writeln('<info>Done</info>');

            $output->writeln('<comment>Upgrading database...</comment>');
            $migration->migrate($databases);
            $output->writeln('<info>All done!</info>');
        }
    }
}
