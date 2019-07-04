<?php
/**
 * Migration Task class.
 */
class GoteoCommunicationModule
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

        CREATE TABLE `filter` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `name` VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL,
          `cert` TINYINT(1) DEFAULT NULL,
          `role` INT(1) DEFAULT NULL,
          `startdate` DATE DEFAULT NULL,
          `enddate` DATE DEFAULT NULL,
          `status` INT(1) DEFAULT NULL,
          `typeofdonor` INT(1) DEFAULT NULL,
          `wallet` TINYINT(1) DEFAULT NULL,
          `project_latitude` DECIMAL(16,14) DEFAULT NULL,
          `project_longitude` DECIMAL(16,14) DEFAULT NULL,
          `project_radius` SMALLINT(6) UNSIGNED DEFAULT NULL,
          `project_location` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB;
        CREATE TABLE `filter_projects` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `filter` INT(11) NOT NULL,
          `project` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );
        CREATE TABLE `filter_calls` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `filter` INT(11) NOT NULL,
          `call` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );
        CREATE TABLE `filter_matcher` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `filter` INT(11) NOT NULL,
          `matcher` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          PRIMARY KEY (`id`),
          FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );
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
     DROP TABLE IF EXISTS `filter_matcher`;
     DROP TABLE IF EXISTS `filter_calls`;
     DROP TABLE IF EXISTS `filter_projects`;
     DROP TABLE IF EXISTS `filter`;
     ";
  }

}