<?php
/**
 * Migration Task class.
 */
class GoteoCallmaxprojects
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
     return "ALTER TABLE `call_conf` ADD COLUMN `max_projects` INT(4) NOT NULL COMMENT 'Max projects to campaign' AFTER `applied`; 
";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return " ALTER TABLE `call_conf` DROP COLUMN `max_projects`;";
  }

}