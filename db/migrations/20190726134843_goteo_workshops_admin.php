<?php
/**
 * Migration Task class.
 */
class GoteoWorkshopsAdmin
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
      ALTER TABLE `workshop` ADD COLUMN `workshop_location` VARCHAR(255) NULL AFTER `blockquote`;
      ALTER TABLE `workshop` ADD COLUMN `lang` VARCHAR(2) NULL AFTER `type`
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
      ALTER TABLE `workshop` DROP COLUMN `workshop_location`;
      ALTER TABLE `workshop` DROP COLUMN `lang`;
     ";
  }

}