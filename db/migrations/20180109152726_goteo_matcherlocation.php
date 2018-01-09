<?php
/**
 * Migration Task class.
 */
class GoteoMatcherlocation
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
        ALTER TABLE `matcher` ADD COLUMN `matcher_location` CHAR(255) NOT NULL AFTER `projects`;
        CREATE TABLE `matcher_location` (
          `id` varchar(50) CHARACTER SET utf8 NOT NULL,
          `latitude` decimal(16,14) NOT NULL,
          `longitude` decimal(16,14) NOT NULL,
          `radius` smallint(6) unsigned NOT NULL DEFAULT '0',
          `method` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'ip',
          `locable` tinyint(1) NOT NULL DEFAULT '0',
          `city` varchar(255) CHARACTER SET utf8 NOT NULL,
          `region` varchar(255) CHARACTER SET utf8 NOT NULL,
          `country` varchar(150) CHARACTER SET utf8 NOT NULL,
          `country_code` varchar(2) CHARACTER SET utf8 NOT NULL,
          `info` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
          `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `latitude` (`latitude`),
          KEY `longitude` (`longitude`),
          CONSTRAINT `matcher_location_ibfk_1` FOREIGN KEY (`id`) REFERENCES `matcher` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
        ALTER TABLE `matcher` DROP COLUMN `matcher_location`;
        DROP TABLE `matcher_location`;
     ";
  }

}
