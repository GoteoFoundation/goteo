<?php
/**
 * Migration Task class.
 */
class GoteoSindexes
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
        ALTER TABLE `invest` ADD INDEX (`invested`), ADD INDEX (`datetime`);
        ALTER TABLE `project` ADD INDEX (`published`), ADD INDEX (`updated`), ADD INDEX (`passed`), ADD INDEX (`success`);
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
        ALTER TABLE `invest` DROP INDEX `invested`, DROP INDEX `datetime`;
        ALTER TABLE `project` DROP INDEX `published`, DROP INDEX `updated`, DROP INDEX `passed`, DROP INDEX `success`;
     ";
  }

}
