<?php
/**
 * Migration Task class.
 */
class GoteoAddImpactDataType
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
        ALTER TABLE `impact_data` ADD COLUMN `type` VARCHAR(50) DEFAULT 'estimation' NOT NULL AFTER `lang`;
        ALTER TABLE `impact_data` ADD COLUMN `source` VARCHAR(50) DEFAULT 'item' NOT NULL AFTER `type`;
        ALTER TABLE `impact_data` ADD COLUMN `icon` varchar(50) COLLATE utf8_general_ci AFTER `image`;
        ALTER TABLE `impact_data` ADD COLUMN `result_msg` TEXT AFTER `image`;
        ALTER TABLE `impact_data_lang` ADD COLUMN `result_msg` TEXT after `description`;
        ALTER TABLE `impact_data` ADD COLUMN `operation_type` VARCHAR(50) DEFAULT NULL;
        ALTER TABLE `impact_data` MODIFY `data_unit` VARCHAR(50);
        CREATE TABLE `impact_data_project` (
            `impact_data_id` BIGINT(20) UNSIGNED NOT NULL,
            `project_id` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            `estimation_amount` INT UNSIGNED NOT NULL,
            `data` INT(10) UNSIGNED NOT NULL,
            FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`impact_data_id`) REFERENCES `impact_data`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `project_impact`(`project_id`,`impact_data_id`)
        );";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
        DROP TABLE `impact_data_project`;
        ALTER TABLE `impact_data` MODIFY `data_unit` VARCHAR(10);
        ALTER TABLE `impact_data` DROP COLUMN `operation_type`;
        ALTER TABLE `impact_data_lang` DROP COLUMN `result_msg`;
        ALTER TABLE `impact_data` DROP COLUMN `result_msg`;
        ALTER TABLE `impact_data` DROP COLUMN `icon`;
        ALTER TABLE `impact_data` DROP COLUMN `source`;
        ALTER TABLE `impact_data` DROP COLUMN `type`;
     ";
  }

}
