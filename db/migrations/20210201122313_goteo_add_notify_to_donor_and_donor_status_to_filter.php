<?php
/**
 * Migration Task class.
 */
class GoteoAddNotifyToDonorAndDonorStatusToFilter
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
        ALTER TABLE `donor` ADD COLUMN `notify` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Notify donor of change in status';
        ALTER TABLE `filter` ADD COLUMN `donor_status` VARCHAR(50) DEFAULT NULL after `invest_status`;
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
        ALTER TABLE `donor` DROP COLUMN `notify`;
        ALTER TABLE `filter` DROP COLUMN `donor_status`;
     ";
  }

}