<?php
/**
 * Migration Task class.
 */
class GoteoFootprintImpactData
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
      CREATE TABLE `footprint_impact` (
        `footprint_id` INT(10) UNSIGNED NOT NULL,
        `impact_data_id` BIGINT(20) UNSIGNED NOT NULL,
        `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        FOREIGN KEY (`footprint_id`) REFERENCES `footprint`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (`impact_data_id`) REFERENCES `impact_data`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
        UNIQUE KEY `footprint_impact`(`footprint_id`,`impact_data_id`)
      );

      ALTER TABLE `footprint` ADD COLUMN `title` VARCHAR(50) DEFAULT NULL AFTER `name`;
      ALTER TABLE `footprint_lang` ADD COLUMN `title` VARCHAR(50) DEFAULT NULL AFTER `name`;
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
      ALTER TABLE `footprint_lang` DROP COLUMN `title`;
      ALTER TABLE `footprint` DROP COLUMN `title`;
      DROP TABLE `footprint_impact`;
     ";
  }

}