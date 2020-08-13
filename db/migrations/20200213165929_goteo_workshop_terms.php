<?php
/**
 * Migration Task class.
 */
class GoteoWorkshopTerms
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
      ALTER TABLE `workshop` ADD COLUMN `terms_file_url` TINYTEXT NULL AFTER `schedule_file_url`;
      ALTER TABLE `workshop` CHANGE `type` `event_type` TINYTEXT CHARSET utf8 COLLATE utf8_general_ci NULL;
      ALTER TABLE `workshop` ADD COLUMN `type` VARCHAR(10) DEFAULT 'md' NULL AFTER `modified`;
      ALTER TABLE `workshop` CHANGE `how_to_get` `how_to_get` TEXT CHARSET utf8 COLLATE utf8_general_ci NULL;
      ALTER TABLE `workshop_lang` CHANGE `how_to_get` `how_to_get` TEXT CHARSET utf8 COLLATE utf8_general_ci NULL;
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
      ALTER TABLE `workshop` DROP COLUMN `terms_file_url`;
      ALTER TABLE `workshop` DROP COLUMN `type`;
      ALTER TABLE `workshop` CHANGE `event_type` `type` TINYTEXT CHARSET utf8 COLLATE utf8_general_ci NULL;
      ALTER TABLE `workshop` CHANGE `how_to_get` `how_to_get` TINYTEXT CHARSET utf8 COLLATE utf8_general_ci NULL;
      ALTER TABLE `workshop_lang` CHANGE `how_to_get` `how_to_get` TINYTEXT CHARSET utf8 COLLATE utf8_general_ci NULL;
     ";
  }

}