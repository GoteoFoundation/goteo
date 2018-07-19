<?php
/**
 * Migration Task class.
 */
class GoteoStoriesMatch
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
      ALTER TABLE `stories` ADD COLUMN `type` TINYTEXT NULL;
      ALTER TABLE `stories` ADD COLUMN `landing_match` TINYINT(1) NOT NULL DEFAULT 0;
      ALTER TABLE `stories` ADD COLUMN `landing_pitch` TINYINT(1) NOT NULL DEFAULT 0;
      ALTER TABLE `stories` ADD COLUMN `sphere` BIGINT(20) UNSIGNED NULL;
      ALTER TABLE `stories` ADD CONSTRAINT `sphere` FOREIGN KEY (`sphere`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE; 
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
      ALTER TABLE `stories` DROP COLUMN `type`;
      ALTER TABLE `stories` DROP COLUMN `landing_match`;
      ALTER TABLE `stories` DROP COLUMN `landing_pitch`;
      ALTER TABLE `stories` DROP COLUMN `sphere`, DROP INDEX `sphere`, DROP FOREIGN KEY `sphere`;
    ";
  }

}