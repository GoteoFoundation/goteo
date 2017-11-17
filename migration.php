<?php
return array(
  'colors' => true,
  'databases' => array(
    'yourdatabase' => array(
      // PDO Connection settings.
      'database_dsn'      => 'mysql:dbname=yourdatabase;host=localhost',
      'database_user'     => 'user',
      'database_password' => 'password',

      // or
      // mysql client command settings.
      // 'mysql_command_enable'    => true,
      // 'mysql_command_cli'       => "/usr/bin/mysql",
      // 'mysql_command_tmpsqldir' => "/tmp",
      // 'mysql_command_host'      => "localhost",
      // 'mysql_command_user'      => "user",
      // 'mysql_command_password'  => "password",
      // 'mysql_command_database'  => "yourdatabase",
      // 'mysql_command_options'   => "--default-character-set=utf8",

      // schema version table
      'schema_version_table'    => 'schema_version',

      // directory contains migration task files.
      'migration_dir'           => '.'
    ),
  ),
);
