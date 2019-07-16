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
          `description` text NOT NULL,
          `cert` TINYINT(1) DEFAULT NULL,
          `role` VARCHAR(50) DEFAULT NULL,
          `startdate` DATE DEFAULT NULL,
          `enddate` DATE DEFAULT NULL,
          `status` VARCHAR(50) DEFAULT NULL,
          `typeofdonor` VARCHAR(50) DEFAULT NULL,
          `foundationdonor` TINYINT(1) DEFAULT NULL,
          `wallet` TINYINT(1) DEFAULT NULL,
          `project_latitude` DECIMAL(16,14) DEFAULT NULL,
          `project_longitude` DECIMAL(16,14) DEFAULT NULL,
          `project_radius` SMALLINT(6) UNSIGNED DEFAULT NULL,
          `project_location` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`)
        );
        CREATE TABLE `filter_projects` (
          `filter` INT(11) NOT NULL,
          `project` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          FOREIGN KEY (`project`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          UNIQUE KEY `id_filterprojects` (`filter`,`project`)
        );
        CREATE TABLE `filter_calls` (
          `filter` INT(11) NOT NULL,
          `call` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          FOREIGN KEY (`call`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          UNIQUE KEY `id_filtercalls` (`filter`,`call`)
        );
        CREATE TABLE `filter_matchers` (
          `filter` INT(11) NOT NULL,
          `matcher` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
          FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
          UNIQUE KEY `id_filtermatcher` (`filter`,`matcher`)
        );

        CREATE TABLE `communication` (
          `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
          `subject` char(255),
          `content` longtext NOT NULL,
          `template` varchar(100) NOT NULL,
          `filter` int(11) NOT NULL,
          `type` char(20) CHARACTER SET utf8 NOT NULL DEFAULT 'md',
          `lang` varchar(3) NOT NULL,
          `header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
          `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `error` tinytext,
          PRIMARY KEY (`id`),
          CONSTRAINT `communication_fk_1` FOREIGN KEY (`filter`) REFERENCES `filter` (`id`) ON UPDATE CASCADE
        );
        CREATE TABLE `communication_lang` (
          `id` bigint(20) UNSIGNED,
          `lang` varchar(3) NOT NULL,
          `subject` char(255) DEFAULT NULL,
          `content` longtext NOT NULL,
          CONSTRAINT `communication_lang_fk` FOREIGN KEY (`id`) REFERENCES `communication` (`id`) ON UPDATE CASCADE
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
     DROP TABLE IF EXISTS `communication_lang`;
     DROP TABLE IF EXISTS `communication`;
     DROP TABLE IF EXISTS `filter`;
     ";
  }

}