<?php
/**
 * Migration Task class.
 */
class GoteoInvestRewardForeign
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
     return "ALTER TABLE `invest_reward` DROP FOREIGN KEY `invest_reward_ibfk_2`; 
             ALTER TABLE `invest_reward` ADD CONSTRAINT `invest_reward_ibfk_2` FOREIGN KEY (`reward`) REFERENCES `reward`(`id`) ON UPDATE CASCADE ON DELETE CASCADE;
            ";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "ALTER TABLE `invest_reward` DROP FOREIGN KEY `invest_reward_ibfk_2`; 
             ALTER TABLE `invest_reward` ADD CONSTRAINT `invest_reward_ibfk_2` FOREIGN KEY (`reward`) REFERENCES `reward`(`id`) ON UPDATE CASCADE";
  }

}