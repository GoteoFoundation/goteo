<?php
/**
 * Migration Task class.
 */
class GoteoContractFee
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
      ALTER TABLE `contract` ADD COLUMN `fee` INT(1) NOT NULL AFTER `paypal_owner`;
      ALTER TABLE `project_account` ALTER `fee` SET DEFAULT 5; 
      UPDATE contract
        INNER JOIN
        project_account
        ON contract.`project` = project_account.`project`
        SET contract.fee = project_account.`fee`;
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
      ALTER TABLE `contract` DROP COLUMN `fee`;
      ALTER TABLE `project_account` ALTER `fee` SET DEFAULT 4;
     ";

  }

}