<?php
/**
 * Migration Task class.
 */
class GoteoMatcherRewards
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
     return "CREATE TABLE `matcher_reward` (
            `matcher` VARCHAR(50) CHARSET utf8 COLLATE utf8_general_ci NOT NULL,
            `reward` BIGINT UNSIGNED NOT NULL,
            `status` TEXT,
            UNIQUE INDEX (`matcher`, `reward`),
            FOREIGN KEY (`matcher`) REFERENCES `matcher`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`reward`) REFERENCES `reward`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
        DROP TABLE `matcher_reward`
     ";
  }

}
