<?php
/**
 * Migration Task class.
 */
class GoteoAddOriginMatcher
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
        ALTER TABLE `origin` ADD COLUMN `channel_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci DEFAULT NULL;
        ALTER TABLE `origin` ADD CONSTRAINT `fk_origin_channel` FOREIGN KEY (`channel_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE; 
        ALTER TABLE `node` ADD COLUMN `analytics_id` varchar(30) DEFAULT NULL;
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
        ALTER TABLE `origin` DROP FOREIGN KEY `fk_origin_channel`;
        ALTER TABLE `origin` DROP COLUMN `channel_id`;
        ALTER TABLE `node` DROP COLUMN `analytics_id`; 
     ";
  }

}