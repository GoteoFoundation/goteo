<?php
/**
 * Migration Task class.
 */
class GoteoSpheresLanding
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
      ALTER TABLE `spheres` ADD COLUMN `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1;
      ALTER TABLE `spheres` ADD COLUMN `landing_match` TINYINT(1) NOT NULL DEFAULT 1;
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
            ALTER TABLE `spheres` DROP COLUMN `order`;
            ALTER TABLE `spheres` DROP COLUMN `landing_match`;

      ";
  }

}