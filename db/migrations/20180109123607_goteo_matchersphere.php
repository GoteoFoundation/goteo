<?php
/**
 * Migration Task class.
 */
class GoteoMatchersphere
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
        CREATE TABLE `matcher_sphere`(
            `matcher` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `sphere` BIGINT UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            UNIQUE INDEX (`matcher`, `sphere`),
            FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`sphere`) REFERENCES `sphere`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
        DROP TABLE `matcher_sphere`;
     ";
  }

}
