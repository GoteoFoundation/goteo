<?php
/**
 * Migration Task class.
 */
class GoteoCreatePostRewardAccess
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
      // return sql query to create table that links post with reward to grant access to the post content
     return "
        ALTER TABLE `post` ADD COLUMN `access_limited` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `allow`;

        CREATE TABLE `post_reward_access` (
            `post_id` bigint(20) unsigned NOT NULL,
            `reward_id` bigint(20) unsigned NOT NULL,
            PRIMARY KEY (`post_id`, `reward_id`),
            FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`reward_id`) REFERENCES `reward` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
        DROP TABLE `post_reward_access`;
        ALTER TABLE `post` DROP COLUMN `access_limited`;
     ";
  }

}
