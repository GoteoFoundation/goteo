<?php
/**
 * Migration Task class.
 */
class GoteoProjectSignUrl
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
        ALTER TABLE `project` ADD COLUMN `sign_url` VARCHAR(255) DEFAULT NULL;
        ALTER TABLE `project_lang` ADD COLUMN `sign_url` VARCHAR(255) DEFAULT NULL;
        ALTER TABLE `project` ADD COLUMN `sign_url_action` VARCHAR(255) DEFAULT NULL;
        ALTER TABLE `project_lang` ADD COLUMN `sign_url_action` VARCHAR(255) DEFAULT NULL;
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
        ALTER TABLE `project` DROP COLUMN `sign_url`;
        ALTER TABLE `project_lang` DROP COLUMN `sign_url`;
        ALTER TABLE `project` DROP COLUMN `sign_url_action`;
        ALTER TABLE `project_lang` DROP COLUMN `sign_url_action`;
    ";
  }

}