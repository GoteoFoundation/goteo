<?php
/**
 * Migration Task class.
 */
class GoteoWorkshopOnline
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
           ALTER TABLE `workshop` ADD COLUMN `online` TINYINT(1) NOT NULL AFTER `subtitle`;
           ALTER TABLE `workshop_lang` ADD COLUMN `blockquote` TINYTEXT NULL AFTER `subtitle`;

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
        ALTER TABLE `workshop` DROP COLUMN `online`;
        ALTER TABLE `workshop_lang` DROP COLUMN `blockquote`;
     ";
  }

}