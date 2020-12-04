<?php
/**
 * Migration Task class.
 */
class GoteoCreateFilterNode
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
        CREATE TABLE `filter_node` (
            `filter` INT(11) NOT NULL,
            `node` VARCHAR(50) NOT NULL COLLATE utf8_general_ci,
            FOREIGN KEY (`filter`) REFERENCES `filter`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`node`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `id_filternodes` (`filter`,`node`)
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
        DROP TABLE `filter_node`;
     ";
  }

}