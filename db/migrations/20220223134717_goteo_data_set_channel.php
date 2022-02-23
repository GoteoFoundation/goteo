<?php
/**
 * Migration Task class.
 */
class GoteoDataSetChannel
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
           CREATE TABLE `node_data_set` (
            `node_id` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            `data_set_id` BIGINT(20) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            FOREIGN KEY (`node_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`data_set_id`) REFERENCES `data_set`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `node_data_set`(`node_id`,`data_set_id`)
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
        DROP TABLE `node_data_set`;
     ";
  }

}
