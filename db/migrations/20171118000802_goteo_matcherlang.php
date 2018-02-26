<?php
/**
 * Migration Task class.
 */
class GoteoMatcherlang
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
     ALTER TABLE `matcher_user` ADD COLUMN `admin` BOOL DEFAULT 0 NOT NULL COMMENT 'If the user is admin' AFTER `pool`;
     CREATE TABLE `matcher_lang`( `id` VARCHAR(50) NOT NULL, `lang` VARCHAR(2), `name` VARCHAR(255), `terms` LONGTEXT, PRIMARY KEY (`id`), FOREIGN KEY (`id`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE ) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
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
     ALTER TABLE `matcher_user` DROP COLUMN `admin`;
     DROP TABLE `matcher_lang`;
     ";
  }

}
