<?php
/**
 * Migration Task class.
 */
class GoteoFootprintImpactData
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
      CREATE TABLE `footprint_impact` (
        `footprint_id` INT(10) UNSIGNED NOT NULL,
        `impact_data_id` INT(20) UNSIGNED NOT NULL,
        `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
        UNIQUE KEY `footprint_impact` (`footprint_id`,`impact_data_id`)
      )
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
      DROP TABLE `footprint_impact`;
     ";
  }

}