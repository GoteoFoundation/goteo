<?php
/**
 * Migration Task class.
 */
class GoteoCallToAction
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
     CREATE TABLE `call_to_action` (
        `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        `header` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `icon` varchar(255) NULL,
        `action` VARCHAR(255) NULL,
        `action_url` TINYTEXT NULL,
        `lang` VARCHAR(6) NOT NULL,
        PRIMARY KEY (`id`)
    );

    CREATE TABLE `call_to_action_lang` (
        `id` BIGINT(20) UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NOT NULL,
        `action` VARCHAR(255) NULL,
        `lang` VARCHAR(6) NOT NULL,
        FOREIGN KEY (`id`) REFERENCES `call_to_action`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
    );
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
        DROP TABLE `call_to_action_lang`;
        DROP TABLE `call_to_action`;
     ";
  }

}