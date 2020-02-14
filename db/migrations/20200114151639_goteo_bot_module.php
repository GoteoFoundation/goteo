<?php
/**
 * Migration Task class.
 */
class GoteoBotModule
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
     return "CREATE TABLE `project_bot` (
              `project` varchar(50) CHARACTER SET utf8 NOT NULL,
              `platform` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
              `channel_id` int(11) NOT NULL,
              UNIQUE KEY `project_platform_channel` (`project`,`platform`, `channel_id`),
              CONSTRAINT `project_bot_ibfk_1` FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            ALTER TABLE `milestone` ADD COLUMN `bot_message` text NULL AFTER `image`;
            ALTER TABLE `milestone_lang` ADD COLUMN `bot_message` text NULL AFTER `description`;
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
      DROP TABLE IF EXISTS `project_bot`;
      ALTER TABLE `milestone` DROP COLUMN `bot_message`;
      ALTER TABLE `milestone_lang` DROP COLUMN `bot_message`;

    ";
  }

}