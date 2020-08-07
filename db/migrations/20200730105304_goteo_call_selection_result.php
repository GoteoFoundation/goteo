<?php
/**
 * Migration Task class.
 */
class GoteoCallSelectionResult
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
      ALTER TABLE `call` ADD COLUMN `selection_result` TINYTEXT;
      ALTER TABLE `call_lang` ADD COLUMN `selection_result` TINYTEXT;
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
      ALTER TABLE `call` DROP COLUMN `selection_result`;
      ALTER TABLE `call_lang` DROP COLUMN `selection_result`;
     ";
  }

}