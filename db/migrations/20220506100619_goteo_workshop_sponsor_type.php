<?php

 use Goteo\Core\Model;

/**
 * Migration Task class.
 */
class GoteoWorkshopSponsorType
{
  public function preUp()
  {
      // add the pre-migration code here
  }

  public function postUp()
  {
      // add the post-migration code here

      $sql = "
        UPDATE `workshop_sponsor` SET type = 'side';
      ";

      Model::query($sql);
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
          ALTER TABLE `workshop_sponsor` ADD COLUMN `type` VARCHAR(255) AFTER `name`;
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
            ALTER TABLE `workshop_sponsor` DROP COLUMN `type`;
     ";
  }

}