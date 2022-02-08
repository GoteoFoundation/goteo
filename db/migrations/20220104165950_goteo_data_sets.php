<?php
/**
 * Migration Task class.
 */
class GoteoDataSets
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
     return "CREATE TABLE `data_set` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` TEXT NULL,
            `description` TEXT NULL,
            `type` VARCHAR(50) NULL,
            `lang` VARCHAR(6) NOT NULL,
            `url` VARCHAR(255) NOT NULL,
            `image` VARCHAR(255) NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `data_set_lang` (
            `id` BIGINT(20) UNSIGNED NOT NULL,
            `title` TEXT NULL,
            `description` TEXT  NULL,
            `lang` VARCHAR(6) NOT NULL,
            UNIQUE KEY (`id`,`lang`),
            CONSTRAINT `data_set_lang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `data_set` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `footprint_data_set` (
            `footprint_id` INT(10) UNSIGNED NOT NULL,
            `data_set_id` BIGINT(20) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            FOREIGN KEY (`footprint_id`) REFERENCES `footprint`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`data_set_id`) REFERENCES `data_set`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `footprint_data_set`(`footprint_id`,`data_set_id`)
        );

        CREATE TABLE `sdg_data_set` (
            `sdg_id` INT(10) UNSIGNED NOT NULL,
            `data_set_id` BIGINT(20) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`data_set_id`) REFERENCES `data_set`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `sdg_data_set`(`sdg_id`,`data_set_id`)
        );

        CREATE TABLE `call_data_set` (
            `call_id` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            `data_set_id` BIGINT(20) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            FOREIGN KEY (`call_id`) REFERENCES `call`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`data_set_id`) REFERENCES `data_set`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `call_data_set`(`call_id`,`data_set_id`)
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
        DROP TABLE `call_data_set`;
        DROP TABLE `sdg_data_set`;
        DROP TABLE `footprint_data_set`;
        DROP TABLE `data_set_lang`;
        DROP TABLE `data_set`;
     ";
  }

}
