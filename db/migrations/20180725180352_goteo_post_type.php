<?php
/**
 * Migration Task class.
 */
class GoteoPostType
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
   * Defaults md type for exiting blog types and html for the others
   * @return string The SQL string to execute for the Up migration.
   */
  public function getUpSQL()
  {
     return "
        ALTER TABLE `post` ADD COLUMN `type` VARCHAR(10) DEFAULT 'md' NOT NULL AFTER `num_comments`;
        UPDATE `post` SET `type`='html' WHERE `blog` IN (SELECT id FROM `blog` WHERE `type`!='project');
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
        ALTER TABLE `post` DROP COLUMN `type`;
     ";
  }

}
