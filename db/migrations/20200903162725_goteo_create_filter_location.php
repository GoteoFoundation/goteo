<?php
/**
 * Migration Task class.
 */
class GoteoCreateFilterLocation
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here
  }

  public function preDown()
  {
      // add the pre-migration code here
  }

  public function postDown()
  {
      // add the post-migration code here
  }

  /**
   * Return the SQL statements for the Up migration
   *
   * @return string The SQL string to execute for the Up migration.
   */
  public function getUpSQL()
  {
     return "
        CREATE TABLE `filter_location` (
            `id` INT(11) NOT NULL,
            `latitude` decimal(16,14) NOT NULL,
            `longitude` decimal(16,14) NOT NULL,
            `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
            `method` varchar(50) NOT NULL DEFAULT 'ip',
            `locable` tinyint(1) NOT NULL DEFAULT '0',
            `city` varchar(255) NOT NULL,
            `region` varchar(255) NOT NULL,
            `country` varchar(150) NOT NULL,
            `country_code` varchar(2) NOT NULL,
            `info` varchar(255) DEFAULT NULL,
            `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `latitude` (`latitude`),
            KEY `longitude` (`longitude`),
            CONSTRAINT `filter_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `filter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ALTER TABLE `filter` ADD COLUMN `filter_location` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL;
        ALTER TABLE `filter` DROP COLUMN `donor_latitude`;
        ALTER TABLE `filter` DROP COLUMN `donor_longitude`;
        ALTER TABLE `filter` DROP COLUMN `donor_radius`;
        ALTER TABLE `filter` DROP COLUMN `donor_location`;
        ALTER TABLE `filter` DROP COLUMN `project_latitude`;
        ALTER TABLE `filter` DROP COLUMN `project_longitude`;
        ALTER TABLE `filter` DROP COLUMN `project_radius`;
        ALTER TABLE `filter` DROP COLUMN `project_location`;

     ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
        DROP TABLE `filter_location`;
        ALTER TABLE `filter` DROP COLUMN `filter_location;
        ALTER TABLE `filter` ADD COLUMN `donor_latitude` DECIMAL(16,14) DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `donor_longitude` DECIMAL(16,14) DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `donor_radius` SMALLINT(6) UNSIGNED DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `donor_location` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `project_latitude` DECIMAL(16,14) DEFAULT NULL,
        ALTER TABLE `filter` ADD COLUMN `project_longitude` DECIMAL(16,14) DEFAULT NULL,
        ALTER TABLE `filter` ADD COLUMN `project_radius` SMALLINT(6) UNSIGNED DEFAULT NULL,
        ALTER TABLE `filter` ADD COLUMN `project_location` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
     ";
  }

}