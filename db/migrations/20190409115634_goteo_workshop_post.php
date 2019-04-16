<?php
/**
 * Migration Task class.
 */
class GoteoWorkshopPost
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
      CREATE TABLE `workshop_post` (
          `workshop_id` BIGINT(20) UNSIGNED NOT NULL,
          `post_id` BIGINT(20) UNSIGNED NOT NULL,
          `order` INT(11),
           PRIMARY KEY (`workshop_id`, `post_id`),
            FOREIGN KEY (`workshop_id`) REFERENCES `workshop`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
            FOREIGN KEY (`post_id`) REFERENCES `post`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
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
        DROP TABLE `workshop_post`;
     ";
  }

}