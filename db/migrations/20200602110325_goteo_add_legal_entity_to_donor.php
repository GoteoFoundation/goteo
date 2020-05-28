<?php
/**
 * Migration Task class.
 */
class GoteoAddLegalEntityToDonor
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
        ALTER TABLE `donor` ADD COLUMN `legal_entity` TINYTEXT AFTER `surname2`;
        ALTER TABLE `donor` ADD COLUMN `legal_document_type` TINYTEXT after `legal_entity`;
        ALTER TABLE `invest_address` ADD COLUMN `legal_entity` TINYTEXT AFTER `name`;
        ALTER TABLE `invest_address` ADD COLUMN `legal_document_type` TINYTEXT after `legal_entity`;
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
        ALTER TABLE `invest_address` DROP COLUMN `legal_document_type`;
        ALTER TABLE `invest_address` DROP COLUMN `legal_entity`;
        ALTER TABLE `donor` DROP COLUMN `legal_document_type`;
        ALTER TABLE `donor` DROP COLUMN `legal_entity`;
     ";
  }

}