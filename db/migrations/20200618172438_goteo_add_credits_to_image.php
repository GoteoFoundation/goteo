<?php
/**
 * Migration Task class.
 */
class GoteoAddCreditsToImage
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
        CREATE TABLE `node_credits` (
            `name` VARCHAR(255) NOT NULL,
            `credits` VARCHAR(255) NOT NULL,
             PRIMARY KEY (`name`)
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
        DROP TABLE `image_credits`;
     ";
  }

}