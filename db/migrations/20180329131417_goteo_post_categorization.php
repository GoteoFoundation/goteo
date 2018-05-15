<?php
/**
 * Migration Task class.
 */
class GoteoPostCategorization
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
      ALTER TABLE `post` ADD COLUMN `section` TINYTEXT NULL AFTER `subtitle`;
      ALTER TABLE `post` ADD COLUMN `glossary` TINYTEXT NULL AFTER `text`;
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
      ALTER TABLE `post` DROP COLUMN `section`;
      ALTER TABLE `post` DROP COLUMN `glossary`;
    ";
  }

}