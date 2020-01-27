<?php
/**
 * Migration Task class.
 */
class GoteoRewardExtraInfo
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
            ALTER TABLE `reward` ADD COLUMN `extra_info_message` TEXT;
            ALTER TABLE `reward_lang` ADD COLUMN `extra_info_message` TEXT;
            ALTER TABLE `invest` ADD COLUMN `extra_info` TEXT;
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
            ALTER TABLE `reward` DROP COLUMN `extra_info_message`;
            ALTER TABLE `reward_lang` DROP COLUMN `extra_info_message`;
            ALTER TABLE `invest` DROP COLUMN `extra_info`;
        ";
  }

}