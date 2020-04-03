<?php
/**
 * Migration Task class.
 */
class GoteoChannelCall
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
        ALTER TABLE `node` ADD COLUMN type VARCHAR(255) DEFAULT 'normal' AFTER `active`;
        ALTER TABLE `node` ADD COLUMN call_inscription_open INT(1) DEFAULT 1 AFTER project_creation_open;
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
        ALTER TABLE `node` DROP COLUMN type;
        ALTER TABLE `node` DROP COLUMN call_inscription_open;
     ";
  }

}