<?php
/**
 * Migration Task class.
 */
class GoteoAddStatusDonor
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
        ALTER TABLE `donor` ADD COLUMN `status` VARCHAR(10),
                            ADD COLUMN `pending` DATETIME,
                            ADD COLUMN `completed` DATETIME,
                            ADD COLUMN `validated` DATETIME,
                            ADD COLUMN `declared` DATETIME;
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
        ALTER TABLE `donor` DROP COLUMN `status`,
                        DROP COLUMN `pending`,
                        DROP COLUMN `completed`,
                        DROP COLUMN `validated`,
                        DROP COLUMN `declared`;
        ";
  }

}