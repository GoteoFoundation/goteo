<?php
/**
 * Migration Task class.
 */
class GoteoSdgProject
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
      CREATE TABLE `sdg_project`(
      `sdg_id` INT(10) UNSIGNED NOT NULL,
      `project_id` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
      `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
      PRIMARY KEY (`sdg_id`, `project_id`),
      FOREIGN KEY (`sdg_id`) REFERENCES `sdg`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
      FOREIGN KEY (`project_id`) REFERENCES `project`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
      );
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
      DROP TABLE sdg_project;
     ";
  }

}