<?php
/**
 * Migration Task class.
 */
class GoteoChannelImpactData
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
        CREATE TABLE `node_impact` (
            `channel_id` VARCHAR(50) COLLATE utf8_general_ci NOT NULL,
            `impact_data_id` BIGINT(20) UNSIGNED NOT NULL,
            `order` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
            FOREIGN KEY (`channel_id`) REFERENCES `node`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`impact_data_id`) REFERENCES `impact_data`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            UNIQUE KEY `footprint_impact`(`channel_id`,`impact_data_id`)
        );";
  }

  /**
   * Return the SQL statements for the Down migration
   *
   * @return string The SQL string to execute for the Down migration.
   */
  public function getDownSQL()
  {
     return "
        DROP TABLE `node_impact`;
     ";
  }

}
