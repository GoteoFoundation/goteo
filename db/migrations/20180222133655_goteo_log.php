<?php
/**
 * Migration Task class.
 */
class GoteoLog
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
        ALTER TABLE `log` CHANGE `target_type` `target_type` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NULL COMMENT 'tipo de objetivo', ADD INDEX (`scope`), ADD INDEX (`target_type`), ADD INDEX (`target_id`);
        ALTER TABLE `log` ADD COLUMN `user_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NULL AFTER `scope`, ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
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
        ALTER TABLE `log` CHANGE `target_type` `target_type` VARCHAR(10) CHARSET utf8 COLLATE utf8_general_ci NULL COMMENT 'tipo de objetivo', DROP INDEX `scope`, DROP INDEX `target_type`, DROP INDEX `target_id`;
        ALTER TABLE `log` DROP COLUMN `user_id`, DROP INDEX `user_id`, DROP FOREIGN KEY `log_ibfk_1`;
     ";
  }

}
