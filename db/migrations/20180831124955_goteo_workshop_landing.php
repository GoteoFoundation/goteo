<?php
/**
 * Migration Task class.
 */
class GoteoWorkshopLanding
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
      ALTER TABLE `workshop` ADD COLUMN `subtitle` TINYTEXT NULL AFTER `title`;
      ALTER TABLE `workshop` ADD COLUMN `type` TINYTEXT NULL AFTER `subtitle`;
      ALTER TABLE `workshop` ADD COLUMN `header_image` VARCHAR(255) NULL AFTER `url`;
      ALTER TABLE `workshop` ADD COLUMN `schedule_file` VARCHAR(255) NULL AFTER `header_image`;
      ALTER TABLE `workshop` ADD COLUMN `how_to_get` TINYTEXT NULL AFTER `schedule_file`;
      ALTER TABLE `workshop` ADD COLUMN `map_iframe` TINYTEXT NULL AFTER `how_to_get`;
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
      ALTER TABLE `workshop` DROP COLUMN `subtitle`;
      ALTER TABLE `workshop` DROP COLUMN `type`;
      ALTER TABLE `workshop` DROP COLUMN `header_image`;
      ALTER TABLE `workshop` DROP COLUMN `schedule_file`;
      ALTER TABLE `workshop` DROP COLUMN `map_iframe`;
      ALTER TABLE `workshop` DROP COLUMN `how_to_get`;
     ";
  }

}