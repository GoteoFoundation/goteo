<?php
/**
 * Migration Task class.
 */
class GoteoChannelResources
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

      CREATE TABLE `node_resource_category` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(150) NOT NULL,
            `icon` varchar(255) NULL,
            `lang` VARCHAR(6) NULL,
            `order` INT(11),
             PRIMARY KEY (`id`)
      );

      CREATE TABLE `node_resource_category_lang` (
          `id` BIGINT(20) UNSIGNED NOT NULL,
          `lang` VARCHAR (6),
          `name` VARCHAR(255) NOT NULL,
          `pending` TINYINT (1),
           FOREIGN KEY (`id`) REFERENCES `node_resource_category`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
        );

      ALTER TABLE `node_resource` ADD COLUMN `image` VARCHAR(255) NULL;
      ALTER TABLE `node_resource` ADD COLUMN `category` BIGINT(20) UNSIGNED NOT NULL;

      SET FOREIGN_KEY_CHECKS=0;
      ALTER TABLE `node_resource` ADD CONSTRAINT `node_resource_category_ibfk_2` FOREIGN KEY (`category`) REFERENCES `node_resource_category` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;
      SET FOREIGN_KEY_CHECKS=1;

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
     ALTER TABLE `node_resource` DROP COLUMN `image`;
     ALTER TABLE `node_resource` DROP FOREIGN KEY `node_resource_category_ibfk_2`;

     ALTER TABLE `node_resource` DROP COLUMN `category`;
     DROP TABLE `node_resource_category_lang`;
     DROP TABLE `node_resource_category`;
     ";
  }

}