<?php
/**
 * Migration Task class.
 */
class GoteoImpactDataItem
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
        CREATE TABLE `impact_item` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL,
            `description` TEXT,
            `unit` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE `impact_data_item` (
            `impact_item_id` BIGINT(20) UNSIGNED NOT NULL,
            `impact_data_id` BIGINT(20) UNSIGNED NOT NULL,
            FOREIGN KEY (`impact_item_id`) REFERENCES `impact_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`impact_data_id`) REFERENCES `impact_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `impact_project_item` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `impact_item_id` BIGINT(20) UNSIGNED NOT NULL,
            `project_id` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            `value` VARCHAR(50) NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`impact_item_id`) REFERENCES `impact_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `impact_project_item_cost` (
            `impact_project_item_id` BIGINT(20) UNSIGNED NOT NULL,
            `cost_id` BIGINT(20) UNSIGNED NOT NULL,
            UNIQUE KEY `impact_cost` (`impact_project_item_id`, `cost_id`),
            FOREIGN KEY (`impact_project_item_id`) REFERENCES `impact_project_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`cost_id`) REFERENCES `cost` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        );

        CREATE TABLE `impact_item_conversion_tip` (
            `impact_item_id` BIGINT(20) UNSIGNED NOT NULL,
            `rate_tip_description` TEXT NOT NULL,
            `reference` TEXT,
            PRIMARY KEY (`impact_item_id`)
        );

        CREATE TABLE `impact_item_footprint` (
            `impact_item_id` BIGINT(20) UNSIGNED NOT NULL,
            `footprint_id` INT(10) UNSIGNED NOT NULL,
            FOREIGN KEY (`impact_item_id`) REFERENCES `impact_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`footprint_id`) REFERENCES `footprint` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
        DROP TABLE `impact_item_footprint`;
        DROP TABLE `impact_item_conversion_tip`;
        DROP TABLE `impact_project_item_cost`;
        DROP TABLE `impact_project_item`;
        DROP TABLE `impact_data_item`;
        DROP TABLE `impact_item`;
     ";
  }

}
