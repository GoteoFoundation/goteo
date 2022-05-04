<?php
/**
 * Migration Task class.
 */
class GoteoInvestOrigin
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
     return "CREATE TABLE `invest_origin` (
        `invest_id` bigint(20) unsigned NOT NULL,
        `source` TEXT NOT NULL,
        `detail` TEXT NOT NULL,
        `allocated` TEXT,
        FOREIGN KEY (`invest_id`) REFERENCES `invest` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
        UNIQUE KEY (`invest_id`)
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
        DROP TABLE `invest_origin`;
     ";
  }

}
