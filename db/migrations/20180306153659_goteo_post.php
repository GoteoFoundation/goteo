<?php
/**
 * Migration Task class.
 */
class GoteoPost
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
            ALTER TABLE `post` ADD COLUMN `subtitle` TINYTEXT NULL AFTER `title`;
            ALTER TABLE `post_lang` ADD COLUMN `subtitle` TINYTEXT NULL AFTER `title`;
            ALTER TABLE `post` ADD COLUMN header_image VARCHAR(255) CHARSET utf8 COLLATE utf8_general_ci NULL AFTER image;
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
            ALTER TABLE `post` DROP COLUMN `subtitle`;
            ALTER TABLE `post_lang` DROP COLUMN `subtitle`;
            ALTER TABLE `post` DROP COLUMN `header_image`;

      ";
  }

}