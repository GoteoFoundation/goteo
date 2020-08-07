<?php
/**
 * Migration Task class.
 */
class GoteoReworkFilterProjectAndInvestStatus
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
        ALTER TABLE `filter` CHANGE `status` `project_status` VARCHAR(50) DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `invest_status` INT(1) DEFAULT NULL;

        ALTER TABLE `filter` ADD COLUMN `donor_latitude` DECIMAL(16,14) DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `donor_longitude` DECIMAL(16,14) DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `donor_radius` SMALLINT(6) UNSIGNED DEFAULT NULL;
        ALTER TABLE `filter` ADD COLUMN `donor_location` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL;
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
        ALTER TABLE `filter` DROP COLUMN `invest_status`;
        ALTER TABLE `filter` CHANGE `project_status` `status` VARCHAR(50) DEFAULT NULL;

        ALTER TABLE `filter` DROP COLUMN `donor_latitude`;
        ALTER TBALE `filter` DROP COLUMN `donor_longitude`;
        ALTER TABLE `filter` DROP COLUMN `donor_radius`;
        ALTER TABLE `filter` DROP COLUMN `donor_location`;
     ";
  }

}