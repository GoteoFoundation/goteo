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
     ";
  }

}