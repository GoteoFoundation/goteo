<?php
/**
 * Migration Task class.
 */
class GoteoSharedMessages
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
      ALTER TABLE `message` ADD COLUMN `shared` TINYINT(1) DEFAULT 0 NOT NULL AFTER `private`;
      ALTER TABLE `mail` CHANGE `template` `template` CHAR(100) NULL;
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
     ALTER TABLE `message` DROP COLUMN `shared`;
     ALTER TABLE `mail` CHANGE `template` `template` BIGINT(20) UNSIGNED NULL;
     ALTER TABLE `mail` ADD CONSTRAINT `mail_ibfk_2` FOREIGN KEY (`template`) REFERENCES `template`(`id`);
     ";
  }

}
