<?php
/**
 * Migration Task class.
 */
class GoteoSubscriptions
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
        CREATE TABLE `subscription` (
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `project` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            `name` VARCHAR(50) NOT NULL,
            `description` TEXT,
            `amount` BIGINT(20) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            FOREIGN KEY (`project`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
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
        DROP TABLE `subscription`;
     ";
  }

}