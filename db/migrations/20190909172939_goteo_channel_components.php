<?php
/**
 * Migration Task class.
 */
class GoteoChannelComponents
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
      CREATE TABLE `node_post` (
          `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `post_id` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           PRIMARY KEY (`node_id`, `post_id`),
            FOREIGN KEY (`node_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`post_id`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

      CREATE TABLE `node_sponsor` (
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `name` TINYTEXT NULL,
          `url` CHAR(255),
          `image` VARCHAR(255),
          `order` INT(11),
          PRIMARY KEY (`id`),
          CONSTRAINT `node_sponsor_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

       CREATE TABLE `node_stories` (
          `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `stories_id` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           PRIMARY KEY (`node_id`, `stories_id`),
            FOREIGN KEY (`node_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`stories_id`) REFERENCES `stories`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        CREATE TABLE `node_resource` (
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `title` VARCHAR(255) NOT NULL,
          `icon` varchar(255) NULL,
          `description` TEXT NOT NULL,
          `action` VARCHAR(255) NOT NULL,
          `action_url` TINYTEXT NULL,
          `action_icon` varchar(255) NULL,
          `lang` VARCHAR(6) NULL,
          `order` INT(11),
           PRIMARY KEY (`id`),
           CONSTRAINT `node_resource_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `node_resource_lang` (
          `id` BIGINT(20) UNSIGNED NOT NULL,
          `lang` VARCHAR (6),
          `title` VARCHAR(255) NOT NULL,
          `description` TEXT NOT NULL,
          `action` VARCHAR(255) NOT NULL,
          `action_url` TINYTEXT NULL,
          `pending` TINYINT (1),
           FOREIGN KEY (`id`) REFERENCES `node_resource`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        ); 

        CREATE TABLE `node_project` (
          `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `project_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `order` INT(11),
           PRIMARY KEY (`node_id`, `project_id`),
            FOREIGN KEY (`node_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

        CREATE TABLE `node_workshop` (
          `node_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
          `workshop_id` BIGINT(20) UNSIGNED NOT NULL,
           PRIMARY KEY (`node_id`, `workshop_id`),
            FOREIGN KEY (`node_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`workshop_id`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );



        ALTER TABLE `node` ADD COLUMN `call_to_action_description` TEXT NOT NULL AFTER `description`;
        ALTER TABLE `node` ADD COLUMN `call_to_action_background_color` TEXT NOT NULL AFTER `owner_font_color`;
        ALTER TABLE `node` ADD COLUMN `hashtag` VARCHAR(255) AFTER `description`;
        ALTER TABLE `node` ADD COLUMN `premium` TINYINT(1) NOT NULL AFTER `hashtag`;
        ALTER TABLE `node_lang` ADD COLUMN `call_to_action_description` TEXT NOT NULL AFTER `description`;
        ALTER TABLE `node_lang` ADD COLUMN `name` VARCHAR(255) AFTER `lang`;

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
       DROP TABLE `node_post`;
       DROP TABLE `node_sponsor`;
       DROP TABLE `node_stories`;
       DROP TABLE `node_resource`;
       DROP TABLE `node_project`;
       DROP TABLE `node_workshop`;
       ALTER TABLE `node` DROP COLUMN `call_to_action_description`;
       ALTER TABLE `node` DROP COLUMN `call_to_action_background_color`;
       ALTER TABLE `node` DROP COLUMN `hashtag`;
       ALTER TABLE `node` DROP COLUMN `premium`;
       ALTER TABLE `node_lang` DROP COLUMN `call_to_action_description`;

     ";
  }

}