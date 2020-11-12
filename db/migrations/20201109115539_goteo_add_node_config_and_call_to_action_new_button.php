<?php
/**
 * Migration Task class.
 */
class GoteoAddNodeConfigAndCallToActionNewButton
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
        ALTER TABLE `node` ADD COLUMN `config` TEXT;

        ALTER TABLE `call_to_action` ADD COLUMN `icon_2` varchar(255) NULL,
                                    ADD COLUMN `action_2` VARCHAR(255) NULL,
                                    ADD COLUMN `action_url_2` TINYTEXT NULL;

        ALTER TABLE `call_to_action_lang` ADD COLUMN `action_2` VARCHAR(255) NULL,
                                        ADD COLUMN `action_url_2` TINYTEXT NULL;
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
        ALTER TABLE `node` DROP COLUMN `config`;
        ALTER TABLE `call_to_action` DROP COLUMN `icon_2`,
                                    DROP COLUMN `action_2`,
                                    DROP COLUMN `action_url_2`;

        ALTER TABLE `call_to_action_lang` DROP COLUMN `action_2`,
                                        DROP COLUMN `action_url_2`;

     ";
  }

}