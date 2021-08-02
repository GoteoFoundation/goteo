<?php
/**
 * Migration Task class.
 */
class GoteoImpactData
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
        CREATE TABLE `impact_data` (
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `title` TEXT NULL,
          `data` VARCHAR(10) NOT NULL,
          `data_unit` VARCHAR(10) NOT NULL,
          `description` TEXT  NULL,
          `image` VARCHAR(255) NULL,
          `lang` VARCHAR(6) NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `impact_data_lang` (
          `id` BIGINT(20) UNSIGNED NOT NULL,
          `title` TEXT NULL,
          `data` VARCHAR(10) NOT NULL,
          `data_unit` VARCHAR(10) NOT NULL,
          `description` TEXT  NULL,
          `lang` VARCHAR(6) NULL,
          UNIQUE KEY (`id`,`lang`),
          CONSTRAINT `impact_data_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `impact_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
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
        DROP TABLE `impact_data_lang`;
        DROP TABLE `impact_data`;
     ";
  }

}