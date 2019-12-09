<?php
/**
 * Migration Task class.
 */
class GoteoMatcherAdmin
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
            ALTER TABLE `matcher` ADD COLUMN description TEXT DEFAULT NULL after `name`;
            ALTER TABLE `matcher_lang` ADD COLUMN description TEXT DEFAULT NULL after `name`;
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
            ALTER TABLE `matcher` DROP COLUMN description;
            ALTER TABLE `matcher_lang` DROP COLUMN description;
     ";
  }

}